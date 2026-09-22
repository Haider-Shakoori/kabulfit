@extends('layouts.app')

@section('content')
<section class="page-hero account-hero">
    <div class="container">
        <p class="eyebrow">{{ __('account.customer_space') }}</p>
        <h1>{{ __('account.title') }}</h1>
        <p>{{ __('account.greeting', ['name' => $user->name]) }}</p>
    </div>
</section>

<section class="section account-section">
    <div class="container account-grid">
        <aside class="account-summary">
            <h2>{{ __('account.profile') }}</h2>
            <dl class="account-facts">
                <div><dt>{{ __('auth.name') }}</dt><dd>{{ $user->name }}</dd></div>
                <div><dt>{{ __('auth.email') }}</dt><dd>{{ $user->email }}</dd></div>
                <div><dt>{{ __('auth.phone') }}</dt><dd>{{ $user->phone ?: '—' }}</dd></div>
                <div><dt>{{ __('account.email_status') }}</dt><dd>{{ $user->hasVerifiedEmail() ? __('account.verified') : __('account.unverified') }}</dd></div>
            </dl>
            @unless ($user->hasVerifiedEmail())
                <form method="post" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}">
                    @csrf
                    <button class="button button-secondary" type="submit">{{ __('auth.resend_verification') }}</button>
                </form>
            @endunless
            <form method="post" action="{{ route('logout', ['locale' => app()->getLocale()]) }}">
                @csrf
                <button class="text-button" type="submit">{{ __('auth.logout') }}</button>
            </form>
        </aside>

        <div class="account-main">
            <x-form-errors />
            <section class="account-panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">{{ __('measurements.custom_tailoring') }}</p><h2>{{ __('measurements.profiles') }}</h2></div>
                    <a class="button button-secondary" href="{{ route('measurements.index', ['locale' => app()->getLocale()]) }}">{{ __('measurements.manage_profiles') }}</a>
                </div>
                <p>{{ __('measurements.profiles_intro') }}</p>
            </section>

            <section class="account-panel">
                <div class="panel-heading">
                    <div><p class="eyebrow">{{ __('account.shipping') }}</p><h2>{{ __('account.addresses') }}</h2></div>
                </div>

                <div class="address-list">
                    @forelse ($user->addresses as $address)
                        <article class="address-card">
                            <div class="address-card-heading">
                                <strong>{{ $address->label ?: __('account.address') }}</strong>
                                @if ($address->is_default)<span class="status-pill">{{ __('account.default') }}</span>@endif
                            </div>
                            <p>{{ $address->recipient_name }} · {{ $address->phone }}</p>
                            <p>{{ $address->address_line1 }}@if($address->address_line2), {{ $address->address_line2 }}@endif</p>
                            <p>{{ $address->city }}@if($address->province), {{ $address->province }}@endif · {{ $address->country_code }}</p>

                            <details>
                                <summary>{{ __('account.edit') }}</summary>
                                <form method="post" action="{{ route('addresses.update', ['locale' => app()->getLocale(), 'address' => $address->uuid]) }}" class="address-form compact-address-form">
                                    @csrf
                                    @method('PUT')
                                    @include('account.partials.address-fields', ['address' => $address])
                                    <button class="button button-primary" type="submit">{{ __('account.save_address') }}</button>
                                </form>
                            </details>

                            <form method="post" action="{{ route('addresses.destroy', ['locale' => app()->getLocale(), 'address' => $address->uuid]) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button class="text-button danger-link" type="submit">{{ __('account.delete') }}</button>
                            </form>
                        </article>
                    @empty
                        <p class="muted">{{ __('account.no_addresses') }}</p>
                    @endforelse
                </div>
            </section>

            <section class="account-panel">
                <div class="panel-heading"><div><p class="eyebrow">{{ __('account.new') }}</p><h2>{{ __('account.add_address') }}</h2></div></div>
                <form method="post" action="{{ route('addresses.store', ['locale' => app()->getLocale()]) }}" class="address-form">
                    @csrf
                    @include('account.partials.address-fields', ['address' => null])
                    <button class="button button-primary" type="submit">{{ __('account.save_address') }}</button>
                </form>
            </section>

            @if ($user->devices->isNotEmpty())
                <section class="account-panel">
                    <div class="panel-heading"><div><p class="eyebrow">{{ __('account.mobile') }}</p><h2>{{ __('account.devices') }}</h2></div></div>
                    <div class="device-list">
                        @foreach ($user->devices as $device)
                            <div class="device-row">
                                <div><strong>{{ $device->name }}</strong><span>{{ ucfirst($device->platform) }}@if($device->app_version) · {{ $device->app_version }}@endif</span></div>
                                <span>{{ $device->revoked_at ? __('account.revoked') : __('account.active') }}</span>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        </div>
    </div>
</section>
@endsection
