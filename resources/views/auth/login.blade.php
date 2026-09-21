@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container auth-shell">
        <div class="auth-copy">
            <p class="eyebrow">{{ __('auth.customer_account') }}</p>
            <h1>{{ __('auth.welcome_back') }}</h1>
            <p>{{ __('auth.login_intro') }}</p>
        </div>
        <div class="auth-card">
            <x-form-errors />
            <form method="post" action="{{ route('login.store', ['locale' => app()->getLocale()]) }}" class="stack-form">
                @csrf
                <label><span>{{ __('auth.email') }}</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                <label><span>{{ __('auth.password') }}</span><input type="password" name="password" required autocomplete="current-password"></label>
                <label class="check-row"><input type="checkbox" name="remember" value="1"><span>{{ __('auth.remember_me') }}</span></label>
                <button class="button button-primary" type="submit">{{ __('auth.login') }}</button>
            </form>
            <div class="auth-links">
                <a href="{{ route('password.request', ['locale' => app()->getLocale()]) }}">{{ __('auth.forgot_password') }}</a>
                <a href="{{ route('register', ['locale' => app()->getLocale()]) }}">{{ __('auth.create_account') }}</a>
            </div>
        </div>
    </div>
</section>
@endsection
