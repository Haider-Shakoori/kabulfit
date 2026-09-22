<aside class="account-summary">
    <h2>Administration</h2>
    <nav class="address-list" aria-label="Admin navigation">
        <a href="{{ route('admin.dashboard', ['locale' => app()->getLocale()]) }}">Dashboard</a>
        @if(auth()->user()->hasPermission('products.manage'))<a href="{{ route('admin.products.index', ['locale' => app()->getLocale()]) }}">Products</a>@endif
        @if(auth()->user()->hasPermission('orders.manage'))<a href="{{ route('admin.orders.index', ['locale' => app()->getLocale()]) }}">Orders</a>@endif
        @if(auth()->user()->hasPermission('customers.manage'))<a href="{{ route('admin.customers.index', ['locale' => app()->getLocale()]) }}">Customers</a>@endif
        @if(auth()->user()->hasPermission('measurements.manage'))<a href="{{ route('admin.measurements.index', ['locale' => app()->getLocale()]) }}">Measurements</a>@endif
        @if(auth()->user()->hasPermission('tailoring.manage'))<a href="{{ route('admin.tailoring.index', ['locale' => app()->getLocale()]) }}">Tailoring</a>@endif
        @if(auth()->user()->hasPermission('payments.view'))<a href="{{ route('admin.payments.index', ['locale' => app()->getLocale()]) }}">Payments</a>@endif
        @if(auth()->user()->hasPermission('content.manage'))<a href="{{ route('admin.content.index', ['locale' => app()->getLocale()]) }}">Content & Journal</a>@endif
        @if(auth()->user()->hasPermission('redirects.manage'))<a href="{{ route('admin.legacy.index', ['locale' => app()->getLocale()]) }}">Legacy URLs</a>@endif
        @if(auth()->user()->hasPermission('settings.manage'))<a href="{{ route('admin.settings.index', ['locale' => app()->getLocale()]) }}">Settings & SEO</a>@endif
        @if(auth()->user()->hasPermission('roles.manage'))<a href="{{ route('admin.roles.index', ['locale' => app()->getLocale()]) }}">Roles</a>@endif
        @if(auth()->user()->hasPermission('audit.view'))<a href="{{ route('admin.audit.index', ['locale' => app()->getLocale()]) }}">Audit log</a>@endif
    </nav>
</aside>
