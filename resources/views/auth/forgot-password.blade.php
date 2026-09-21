@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container auth-shell auth-shell-single">
        <div class="auth-card">
            <p class="eyebrow">{{ __('auth.security') }}</p>
            <h1>{{ __('auth.forgot_password') }}</h1>
            <p>{{ __('auth.forgot_intro') }}</p>
            <x-form-errors />
            <form method="post" action="{{ route('password.email', ['locale' => app()->getLocale()]) }}" class="stack-form">
                @csrf
                <label><span>{{ __('auth.email') }}</span><input type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></label>
                <button class="button button-primary" type="submit">{{ __('auth.send_reset_link') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection
