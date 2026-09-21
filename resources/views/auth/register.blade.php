@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container auth-shell">
        <div class="auth-copy">
            <p class="eyebrow">{{ __('auth.customer_account') }}</p>
            <h1>{{ __('auth.create_account') }}</h1>
            <p>{{ __('auth.register_intro') }}</p>
        </div>
        <div class="auth-card">
            <x-form-errors />
            <form method="post" action="{{ route('register.store', ['locale' => app()->getLocale()]) }}" class="stack-form">
                @csrf
                <input type="hidden" name="preferred_locale" value="{{ app()->getLocale() }}">
                <label><span>{{ __('auth.name') }}</span><input name="name" value="{{ old('name') }}" required autocomplete="name"></label>
                <label><span>{{ __('auth.email') }}</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                <label><span>{{ __('auth.phone_optional') }}</span><input type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"></label>
                <label><span>{{ __('auth.password') }}</span><input type="password" name="password" required autocomplete="new-password"></label>
                <label><span>{{ __('auth.confirm_password') }}</span><input type="password" name="password_confirmation" required autocomplete="new-password"></label>
                <button class="button button-primary" type="submit">{{ __('auth.register') }}</button>
            </form>
            <div class="auth-links"><a href="{{ route('login', ['locale' => app()->getLocale()]) }}">{{ __('auth.already_registered') }}</a></div>
        </div>
    </div>
</section>
@endsection
