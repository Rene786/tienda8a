<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('home', $this->metrics());
    }

    public function report(string $metric): Response
    {
        $report = match ($metric) {
            'daily-sales' => $this->dailySalesReport(),
            'monthly-sales' => $this->monthlySalesReport(),
            'products' => $this->productsReport(),
            'users' => $this->usersReport(),
            'inventory' => $this->inventoryReport(),
            default => abort(404),
        };

        return Pdf::loadView('reports_dashboard', [
            'metric' => $metric,
            'report' => $report,
            'generatedAt' => now(),
        ])->download('reporte-'.$metric.'-techstore.pdf');
    }

    /**
     * @return array{dailySales: float, monthlySales: float, registeredProducts: int, registeredUsers: int, inventoryValue: float, generatedAt: Carbon}
     */
    private function metrics(): array
    {
        $today = now();
        $paidOrders = Order::query()->where('payment_status', 'paid');

        return [
            'dailySales' => (float) (clone $paidOrders)->whereDate('created_at', $today->toDateString())->sum('total'),
            'monthlySales' => (float) (clone $paidOrders)->whereBetween('created_at', [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()])->sum('total'),
            'registeredProducts' => Product::query()->count(),
            'registeredUsers' => User::withTrashed()->count(),
            'inventoryValue' => (float) Product::query()->sum(DB::raw('price * stock')),
            'generatedAt' => $today,
        ];
    }

    /**
     * @return array{title: string, total: float, count: int, orders: Collection<int, Order>}
     */
    private function dailySalesReport(): array
    {
        $orders = Order::with(['user' => fn ($query) => $query->withTrashed()])
            ->where('payment_status', 'paid')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        return [
            'title' => 'Ventas de hoy',
            'total' => (float) $orders->sum('total'),
            'count' => $orders->count(),
            'orders' => $orders,
        ];
    }

    /**
     * @return array{title: string, period: string, total: float, count: int, orders: Collection<int, Order>}
     */
    private function monthlySalesReport(): array
    {
        $periodStart = now()->startOfMonth();
        $periodEnd = now()->endOfMonth();
        $orders = Order::with(['user' => fn ($query) => $query->withTrashed()])
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$periodStart, $periodEnd])
            ->latest()
            ->get();

        return [
            'title' => 'Ventas del mes',
            'period' => now()->locale('es')->translatedFormat('F Y'),
            'total' => (float) $orders->sum('total'),
            'count' => $orders->count(),
            'orders' => $orders,
        ];
    }

    /**
     * @return array{title: string, products: Collection<int, Product>, count: int, active: int, inactive: int, outOfStock: int}
     */
    private function productsReport(): array
    {
        $products = Product::with('category')->orderBy('name')->get();

        return [
            'title' => 'Productos registrados',
            'products' => $products,
            'count' => $products->count(),
            'active' => $products->where('status', true)->count(),
            'inactive' => $products->where('status', false)->count(),
            'outOfStock' => $products->where('stock', 0)->count(),
        ];
    }

    /**
     * @return array{title: string, users: Collection<int, User>, count: int, active: int, inactive: int}
     */
    private function usersReport(): array
    {
        $users = User::withTrashed()->orderBy('name')->get();

        return [
            'title' => 'Usuarios registrados',
            'users' => $users,
            'count' => $users->count(),
            'active' => $users->whereNull('deleted_at')->count(),
            'inactive' => $users->whereNotNull('deleted_at')->count(),
        ];
    }

    /**
     * @return array{title: string, products: Collection<int, Product>, total: float}
     */
    private function inventoryReport(): array
    {
        $products = Product::with('category')->orderBy('name')->get();

        return [
            'title' => 'Valor del inventario',
            'products' => $products,
            'total' => (float) $products->sum(fn (Product $product): float => (float) $product->price * $product->stock),
        ];
    }
}
