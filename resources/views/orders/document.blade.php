<!doctype html>
<html lang="{{ app()->getLocale() }}" dir="{{ in_array(app()->getLocale(), config('kabulfit.rtl_locales'), true) ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>KabulFit · {{ $order->number }}</title>
    <style>
        *{box-sizing:border-box} body{font-family:Arial,sans-serif;color:#1f2937;margin:0;background:#f8fafc}
        .page{max-width:860px;margin:24px auto;background:#fff;padding:32px;border:1px solid #e5e7eb;border-radius:16px}
        .toolbar{max-width:860px;margin:20px auto 0;display:flex;gap:10px;justify-content:flex-end}
        .btn{border:1px solid #d1d5db;background:#fff;color:#374151;padding:10px 14px;border-radius:8px;font-weight:700;cursor:pointer;text-decoration:none}
        .btn-primary{background:#881C27;border-color:#881C27;color:#fff}
        .header{display:flex;justify-content:space-between;gap:24px;align-items:flex-start;border-bottom:3px solid #881C27;padding-bottom:20px}
        .brand{font-size:32px;font-weight:800;color:#881C27}.muted{color:#6b7280}.small{font-size:12px}
        .grid{display:grid;grid-template-columns:1fr 1fr;gap:20px}.section{margin-top:24px}.title{font-size:13px;font-weight:800;color:#881C27;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px}
        .card{border:1px solid #e5e7eb;border-radius:12px;padding:14px}.item{display:grid;grid-template-columns:72px 1fr auto;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6}.item:last-child{border-bottom:0}
        .item img{width:72px;height:72px;object-fit:cover;border-radius:10px;background:#f3f4f6}.pill{display:inline-block;background:#f3f4f6;padding:4px 8px;border-radius:999px;font-size:11px;margin:3px 4px 0 0}
        table{width:100%;border-collapse:collapse} td{padding:6px 0}.right{text-align:end}.total{font-size:20px;font-weight:800;color:#881C27;border-top:1px solid #e5e7eb;padding-top:12px}
        @media(max-width:700px){.page{margin:0;border:0;border-radius:0;padding:20px}.toolbar{padding:0 16px}.grid{grid-template-columns:1fr}.item{grid-template-columns:56px 1fr}.item img{width:56px;height:56px}.item>div:last-child{grid-column:2}}
        @media print{body{background:#fff}.toolbar{display:none}.page{max-width:none;margin:0;border:0;border-radius:0;padding:0}.item{break-inside:avoid}.section{break-inside:avoid}}
    </style>
</head>
<body>
<div class="toolbar">
    <a class="btn" href="{{ $isAdmin ? route('admin.orders.show', ['locale' => app()->getLocale(), 'order' => $order]) : route('orders.show', ['locale' => app()->getLocale(), 'order' => $order]) }}">Back</a>
    <button class="btn btn-primary" type="button" onclick="window.print()">Print / Save PDF</button>
</div>

<main class="page">
    <header class="header">
        <div>
            <div class="brand">KabulFit</div>
            <div class="muted">Order Summary</div>
        </div>
        <div class="right">
            <strong>{{ $order->number }}</strong>
            <div class="small muted">{{ $order->created_at?->translatedFormat('F j, Y · H:i') }}</div>
            <div class="small" style="margin-top:6px;text-transform:capitalize">{{ str($order->status)->replace('_', ' ') }}</div>
        </div>
    </header>

    <section class="section grid">
        <div class="card">
            <div class="title">Customer</div>
            <strong>{{ $order->user?->name ?: 'Customer' }}</strong>
            <div class="small muted" style="margin-top:4px">{{ $order->user?->email }}</div>
            @if($order->user?->phone)<div class="small muted">{{ $order->user->phone }}</div>@endif
        </div>

        @php($address = $order->shipping_address ?? [])
        <div class="card">
            <div class="title">Shipping Address</div>
            <strong>{{ $address['recipient_name'] ?? $address['full_name'] ?? '' }}</strong>
            <div class="small muted" style="margin-top:4px">{{ $address['address_line1'] ?? '' }}</div>
            @if(!empty($address['address_line2']))<div class="small muted">{{ $address['address_line2'] }}</div>@endif
            <div class="small muted">{{ $address['city'] ?? '' }}@if(!empty($address['province'])), {{ $address['province'] }}@endif @if(!empty($address['postal_code'])) {{ $address['postal_code'] }}@endif</div>
            <div class="small muted">{{ $address['country_code'] ?? $address['country'] ?? '' }}</div>
            @if(!empty($address['phone']))<div class="small muted">{{ $address['phone'] }}</div>@endif
        </div>
    </section>

    <section class="section">
        <div class="title">Order Items</div>
        <div class="card">
            @foreach($order->items as $item)
                <article class="item">
                    <div>
                        @if($item->product?->primaryMedia)
                            <img src="{{ asset($item->product->primaryMedia->path) }}" alt="{{ $item->name }}">
                        @else
                            <div style="width:72px;height:72px;border-radius:10px;background:#f3f4f6;display:grid;place-items:center;font-weight:800;color:#9ca3af">KF</div>
                        @endif
                    </div>
                    <div>
                        <strong>{{ $item->name }}</strong>
                        <div class="small muted" style="margin-top:4px">{{ $item->sku }}@if($item->variant_label) · {{ $item->variant_label }}@endif</div>
                        <span class="pill">Qty {{ $item->quantity }}</span>
                        @if($item->is_custom_tailored)<span class="pill">Custom tailored</span>@endif
                        @if($item->tailoring_notes)<div class="small" style="margin-top:8px"><strong>Note:</strong> {{ $item->tailoring_notes }}</div>@endif

                        @if($item->measurements->isNotEmpty())
                            <div style="margin-top:10px">
                                @foreach($item->measurements as $measurement)
                                    <span class="pill">{{ $measurement->definition_name }}: {{ $measurement->value_cm }} cm</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="right">
                        <strong>{{ number_format($item->line_total_minor / 100, 2) }} {{ $order->currency }}</strong>
                        <div class="small muted">{{ number_format($item->unit_price_minor / 100, 2) }} × {{ $item->quantity }}</div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="section grid">
        <div class="card">
            <div class="title">Shipping</div>
            <div class="small muted">Method</div>
            <strong>{{ $order->shipping_method_code }}</strong>
            @if($order->coupon_code)
                <div class="small muted" style="margin-top:10px">Discount code</div>
                <strong>{{ $order->coupon_code }}</strong>
            @endif
        </div>

        <div class="card">
            <div class="title">Totals</div>
            <table>
                <tr><td>Subtotal</td><td class="right">{{ number_format($order->subtotal_minor / 100, 2) }} {{ $order->currency }}</td></tr>
                @if($order->discount_minor > 0)<tr><td>Discount</td><td class="right">-{{ number_format($order->discount_minor / 100, 2) }} {{ $order->currency }}</td></tr>@endif
                <tr><td>Shipping</td><td class="right">{{ number_format($order->shipping_minor / 100, 2) }} {{ $order->currency }}</td></tr>
                <tr><td class="total">Total</td><td class="right total">{{ number_format($order->total_minor / 100, 2) }} {{ $order->currency }}</td></tr>
            </table>
        </div>
    </section>

    <footer class="section small muted" style="text-align:center;border-top:1px solid #e5e7eb;padding-top:18px">
        Generated by KabulFit · {{ now()->translatedFormat('F j, Y · H:i') }}
    </footer>
</main>
</body>
</html>
