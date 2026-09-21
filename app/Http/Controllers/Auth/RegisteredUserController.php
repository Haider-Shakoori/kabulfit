<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(string $locale): View
    {
        $seo = PrivatePageSeo::make(__('auth.register'), route('register', ['locale' => $locale]));

        return view('auth.register', compact('seo'));
    }

    public function store(RegisterRequest $request, string $locale): RedirectResponse
    {
        $user = User::query()->create($request->safe()->only([
            'name', 'email', 'phone', 'preferred_locale', 'password',
        ]));

        event(new Registered($user));
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('verification.notice', ['locale' => $locale]);
    }
}
