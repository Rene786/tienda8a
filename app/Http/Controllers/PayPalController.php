<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\PayPalService;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class PayPalController extends Controller
{
    public function checkout(Request $request, PayPalService $paypal): RedirectResponse
    {
        if (! $paypal->hasSandboxCredentials()) {
            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'Configura las credenciales Sandbox de PayPal para iniciar el pago de prueba.',
            ]);
        }

        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $order = DB::transaction(function () use ($user): Order {
            $cart = $user->cart()->with('items.product')->first();

            if ($cart === null || $cart->items->isEmpty()) {
                throw ValidationException::withMessages([
                    'cart' => 'Agrega productos al carrito antes de pagar.',
                ]);
            }

            $orderItems = [];
            $total = 0;

            foreach ($cart->items as $cartItem) {
                $product = Product::query()
                    ->where('status', true)
                    ->lockForUpdate()
                    ->find($cartItem->product_id);

                if ($product === null || $cartItem->quantity > $product->stock) {
                    throw ValidationException::withMessages([
                        'cart' => 'Uno o más productos ya no están disponibles en la cantidad seleccionada. Revisa tu carrito.',
                    ]);
                }

                $subtotal = round((float) $product->price * $cartItem->quantity, 2);
                $total += $subtotal;
                $orderItems[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $product->price,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => number_format($subtotal, 2, '.', ''),
                ];
            }

            $order = $user->orders()->create([
                'order_number' => 'TS-'.Str::upper((string) Str::uuid()),
                'total' => number_format($total, 2, '.', ''),
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);
            $order->items()->createMany($orderItems);

            return $order;
        });

        try {
            $paypalOrder = $paypal->createOrder($order);
        } catch (RequestException $exception) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);
            $this->logProviderFailure($exception, $order, 'create_order');

            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'PayPal no pudo iniciar el pago de prueba. Revisa el Client ID y Secret Sandbox e inténtalo de nuevo.',
            ]);
        } catch (RuntimeException $exception) {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);
            Log::warning('PayPal no devolvió una orden de Sandbox válida.', [
                'order_number' => $order->order_number,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'PayPal no devolvió una orden de prueba válida. Inténtalo de nuevo.',
            ]);
        }

        $order->update(['paypal_order_id' => $paypalOrder['order_id']]);

        return redirect()->away($paypalOrder['approval_url']);
    }

    public function returnFromPayPal(Request $request, PayPalService $paypal): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $paypalOrderId = $request->query('token');
        abort_unless(is_string($paypalOrderId) && $paypalOrderId !== '', 404);

        $order = $user->orders()
            ->where('paypal_order_id', $paypalOrderId)
            ->firstOrFail();

        if ($order->payment_status === 'paid') {
            return redirect()->route('store-cart.index')
                ->with('success', 'El pago ya fue confirmado. Pedido '.$order->order_number.'.');
        }

        try {
            $capture = $paypal->captureOrder($paypalOrderId);
        } catch (RequestException $exception) {
            $this->logProviderFailure($exception, $order, 'capture');

            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'PayPal no pudo confirmar el pago. Revisa el estado en tu perfil antes de volver a intentarlo.',
            ]);
        } catch (RuntimeException $exception) {
            Log::warning('PayPal no pudo confirmar la orden.', [
                'order_number' => $order->order_number,
                'message' => $exception->getMessage(),
            ]);

            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'PayPal no pudo confirmar el pago. Inténtalo de nuevo más tarde.',
            ]);
        }

        $captureDetails = $capture['purchase_units'][0]['payments']['captures'][0] ?? null;
        $capturedAmount = is_array($captureDetails) ? ($captureDetails['amount'] ?? null) : null;
        $captureIsValid = ($capture['status'] ?? null) === 'COMPLETED'
            && is_array($captureDetails)
            && ($captureDetails['status'] ?? null) === 'COMPLETED'
            && is_array($capturedAmount)
            && ($capturedAmount['currency_code'] ?? null) === config('services.paypal.currency_code')
            && is_numeric($capturedAmount['value'] ?? null)
            && number_format((float) $capturedAmount['value'], 2, '.', '') === number_format((float) $order->total, 2, '.', '');

        if (! $captureIsValid) {
            Log::warning('La captura de PayPal no coincide con el pedido.', [
                'order_number' => $order->order_number,
                'paypal_order_id' => $paypalOrderId,
                'capture_status' => $capture['status'] ?? null,
                'capture_amount' => $capturedAmount,
            ]);

            return redirect()->route('store-cart.index')->withErrors([
                'payment' => 'No se pudo validar el importe del pago. El pedido permanece pendiente; contacta a soporte antes de volver a pagar.',
            ]);
        }

        DB::transaction(function () use ($order, $user): void {
            $lockedOrder = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->payment_status !== 'paid') {
                $lockedOrder->update([
                    'payment_status' => 'paid',
                    'status' => 'processing',
                ]);
                $user->cart()->first()?->items()->delete();
            }
        });

        return redirect()->route('store-cart.index')
            ->with('success', 'Pago de prueba aprobado. Pedido '.$order->order_number.'.');
    }

    public function cancel(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user instanceof User, 401);

        $orderNumber = $request->query('order');
        abort_unless(is_string($orderNumber) && $orderNumber !== '', 404);

        $order = $user->orders()->where('order_number', $orderNumber)->firstOrFail();

        if ($order->payment_status === 'pending') {
            $order->update([
                'status' => 'cancelled',
                'payment_status' => 'failed',
            ]);
        }

        return redirect()->route('store-cart.index')
            ->with('success', 'Cancelaste el pago en PayPal. Puedes volver a intentarlo desde tu carrito.');
    }

    private function logProviderFailure(RequestException $exception, Order $order, string $operation): void
    {
        Log::warning('PayPal rechazó una solicitud de Checkout.', [
            'order_number' => $order->order_number,
            'operation' => $operation,
            'http_status' => $exception->response->status(),
            'provider_name' => $exception->response->json('name'),
            'provider_message' => $exception->response->json('message'),
            'provider_issue' => $exception->response->json('details.0.issue'),
        ]);
    }
}
