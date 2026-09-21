<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(string $locale): View
    {
        $seo = PrivatePageSeo::make(__('auth.login'), route('login', ['locale' => $locale]));

        return view('auth.login', compact('seo'));
    }

    public function store(LoginRequest $request, string $locale): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();
        $request->user()->update(['preferred_locale' => $locale]);

        return redirect()->intended(route('account', ['locale' => $locale]));
    }

    public function destroy(Request $request, string $locale): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home', ['locale' => $locale]);
    }
}
