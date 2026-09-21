<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    public function create(string $locale): View
    {
        $seo = PrivatePageSeo::make(__('auth.forgot_password'), route('password.request', ['locale' => $locale]));

        return view('auth.forgot-password', compact('seo'));
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email:rfc']]);

        Password::sendResetLink(['email' => mb_strtolower(trim((string) $request->input('email')))]);

        return back()->with('status', __('auth.reset_link_sent'));
    }
}
