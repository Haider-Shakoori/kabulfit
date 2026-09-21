@extends('layouts.app')

@section('content')
<section class="auth-section">
    <div class="container auth-shell auth-shell-single">
        <div class="auth-card">
            <p class="eyebrow">{{ __('auth.security') }}</p>
            <h1>{{ __('auth.verify_email') }}</h1>
            <p>{{ __('auth.verify_intro') }}</p>
            <x-form-errors />
            <form method="post" action="{{ route('verification.send', ['locale' => app()->getLocale()]) }}">
                @csrf
                <button class="button button-primary" type="submit">{{ __('auth.resend_verification') }}</button>
            </form>
            <form method="post" action="{{ route('logout', ['locale' => app()->getLocale()]) }}" class="inline-form">
                @csrf
                <button class="text-button" type="submit">{{ __('auth.logout') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection
