<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $report['title'] }} - TechStore</title>
    <style>
        @page { margin: 36px; }
        body { color: #18181b; font-family: Arial, sans-serif; font-size: 12px; }
        .header { border-bottom: 2px solid #2563eb; margin-bottom: 20px; padding-bottom: 12px; }
        .header-table, .detail-table { border-collapse: collapse; width: 100%; }
        .header-title { color: #0f172a; font-size: 22px; margin: 0 0 6px; }
        .subtitle, .meta { color: #71717a; font-size: 11px; }
        .meta { text-align: right; }
        .summary { background: #eff6ff; border: 1px solid #bfdbfe; margin: 18px 0; padding: 12px; }
        .summary-total { color: #1d4ed8; font-size: 20px; font-weight: bold; margin: 8px 0; }
        .detail-table { margin-top: 18px; }
        .detail-table th { background: #f4f4f5; color: #3f3f46; font-size: 10px; text-align: left; text-transform: uppercase; }
        .detail-table th, .detail-table td { border-bottom: 1px solid #e4e4e7; padding: 8px 6px; }
        .amount { text-align: right; white-space: nowrap; }
        .grand-total { font-size: 15px; font-weight: bold; text-align: right; }
        .footer { border-top: 1px solid #e4e4e7; color: #a1a1aa; font-size: 10px; margin-top: 30px; padding-top: 8px; text-align: center; }
    </style>
</head>
<body>
    <header class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1 class="header-title">{{ $report['title'] }}</h1>
                    <div class="subtitle">Reporte individual de TechStore</div>
                </td>
                <td class="meta">
                    <strong>Fecha de emisión:</strong><br>
                    {{ $generatedAt->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </header>

    @if (in_array($metric, ['daily-sales', 'monthly-sales'], true))
        <div class="summary">
            @if ($metric === 'monthly-sales')
                <div>Periodo: {{ ucfirst($report['period']) }}</div>
            @else
                <div>Fecha: {{ $generatedAt->format('d/m/Y') }}</div>
            @endif
            <div class="summary-total">${{ number_format($report['total'], 2) }} MXN</div>
            <div>Correspondientes a {{ number_format($report['count']) }} pedidos pagados.</div>
        </div>
        <table class="detail-table">
            <thead><tr><th>Número de pedido</th><th>Cliente</th><th>Fecha</th><th class="amount">Importe</th></tr></thead>
            <tbody>
                @forelse ($report['orders'] as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->user?->name ?? 'Usuario no disponible' }}</td>
                        <td>{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                        <td class="amount">${{ number_format((float) $order->total, 2) }} MXN</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay pedidos pagados en este periodo.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p class="grand-total">Total general: ${{ number_format($report['total'], 2) }} MXN</p>
    @elseif ($metric === 'products')
        <div class="summary">
            Total: <strong>{{ number_format($report['count']) }}</strong> ·
            Vigentes: {{ number_format($report['active']) }} ·
            Inactivos: {{ number_format($report['inactive']) }} ·
            Sin existencias: {{ number_format($report['outOfStock']) }}
        </div>
        <table class="detail-table">
            <thead><tr><th>Producto</th><th>Categoría</th><th class="amount">Precio</th><th class="amount">Existencias</th><th>Estado</th></tr></thead>
            <tbody>
                @forelse ($report['products'] as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->category?->name ?? 'Sin categoría' }}</td>
                        <td class="amount">${{ number_format((float) $product->price, 2) }} MXN</td>
                        <td class="amount">{{ number_format($product->stock) }}</td>
                        <td>{{ $product->status ? 'Vigente' : 'Inactivo' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($metric === 'users')
        <div class="summary">
            Total: <strong>{{ number_format($report['count']) }}</strong> ·
            Vigentes: {{ number_format($report['active']) }} ·
            Inactivos: {{ number_format($report['inactive']) }}
        </div>
        <table class="detail-table">
            <thead><tr><th>Nombre</th><th>Correo electrónico</th><th>Fecha de registro</th><th>Rol</th><th>Estado</th></tr></thead>
            <tbody>
                @forelse ($report['users'] as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->created_at?->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($user->role ?? 'cliente') }}</td>
                        <td>{{ $user->trashed() ? 'Inactivo' : 'Vigente' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No hay usuarios registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    @elseif ($metric === 'inventory')
        <table class="detail-table">
            <thead><tr><th>Producto</th><th class="amount">Precio unitario</th><th class="amount">Existencias</th><th class="amount">Valor</th></tr></thead>
            <tbody>
                @forelse ($report['products'] as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td class="amount">${{ number_format((float) $product->price, 2) }} MXN</td>
                        <td class="amount">{{ number_format($product->stock) }}</td>
                        <td class="amount">${{ number_format((float) $product->price * $product->stock, 2) }} MXN</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No hay productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
        <p class="grand-total">Valor total del inventario: ${{ number_format($report['total'], 2) }} MXN</p>
        <p class="subtitle">Fórmula: precio unitario × existencias disponibles.</p>
    @endif

    <footer class="footer">Reporte automático generado por TechStore · {{ $generatedAt->format('Y') }}</footer>
</body>
</html>
