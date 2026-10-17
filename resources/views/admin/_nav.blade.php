@php
    $items = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'permission' => null, 'match' => 'admin.dashboard'],
        ['label' => 'Products', 'route' => 'admin.products.index', 'permission' => 'products.manage', 'match' => 'admin.products.*'],
        ['label' => 'Orders', 'route' => 'admin.orders.index', 'permission' => 'orders.manage', 'match' => 'admin.orders.*'],
        ['label' => 'Customers', 'route' => 'admin.customers.index', 'permission' => 'customers.manage', 'match' => 'admin.customers.*'],
        ['label' => 'Measurements', 'route' => 'admin.measurements.index', 'permission' => 'measurements.manage', 'match' => 'admin.measurements.*'],
        ['label' => 'Tailoring', 'route' => 'admin.tailoring.index', 'permission' => 'tailoring.manage', 'match' => 'admin.tailoring.*'],
        ['label' => 'Payments', 'route' => 'admin.payments.index', 'permission' => 'payments.view', 'match' => 'admin.payments.*'],
        ['label' => 'Content & Journal', 'route' => 'admin.content.index', 'permission' => 'content.manage', 'match' => 'admin.content.*'],
        ['label' => 'Legacy URLs', 'route' => 'admin.legacy.index', 'permission' => 'redirects.manage', 'match' => 'admin.legacy.*'],
        ['label' => 'Settings & SEO', 'route' => 'admin.settings.index', 'permission' => 'settings.manage', 'match' => 'admin.settings.*'],
        ['label' => 'Roles', 'route' => 'admin.roles.index', 'permission' => 'roles.manage', 'match' => 'admin.roles.*'],
        ['label' => 'Audit log', 'route' => 'admin.audit.index', 'permission' => 'audit.view', 'match' => 'admin.audit.*'],
    ];
@endphp

<aside class="self-start lg:sticky lg:top-36">
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <div class="border-b border-gray-100 p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#2A6867]">KabulFit</p>
            <h2 class="mt-1 text-lg font-semibold text-gray-900">Administration</h2>
            <p class="mt-1 text-xs text-gray-500">{{ auth()->user()->email }}</p>
        </div>
        <nav class="grid p-2" aria-label="Admin navigation">
            @foreach($items as $item)
                @if($item['permission'] === null || auth()->user()->hasPermission($item['permission']))
                    <a href="{{ route($item['route'], ['locale' => app()->getLocale()]) }}"
                       @class([
                           'rounded-xl px-4 py-3 text-sm font-medium transition',
                           'bg-gradient-to-r from-[#881C27] to-[#2A6867] text-white shadow-sm' => request()->routeIs($item['match']),
                           'text-gray-600 hover:bg-gray-50 hover:text-[#881C27]' => ! request()->routeIs($item['match']),
                       ])>
                        {{ $item['label'] }}
                    </a>
                @endif
            @endforeach
        </nav>
    </div>
</aside>
