@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container auth-shell auth-shell-single">
        <div class="auth-card">
            <p class="eyebrow">{{ __('auth.security') }}</p>
            <h1>{{ __('auth.reset_password') }}</h1>
            <x-form-errors />
            <form method="post" action="{{ route('password.update', ['locale' => app()->getLocale()]) }}" class="stack-form">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <label><span>{{ __('auth.email') }}</span><input type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email"></label>
                <label><span>{{ __('auth.new_password') }}</span><input type="password" name="password" required autocomplete="new-password"></label>
                <label><span>{{ __('auth.confirm_password') }}</span><input type="password" name="password_confirmation" required autocomplete="new-password"></label>
                <button class="button button-primary" type="submit">{{ __('auth.reset_password') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection
