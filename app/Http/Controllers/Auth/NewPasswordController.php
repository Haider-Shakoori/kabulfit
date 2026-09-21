<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request, string $locale, string $token): View
    {
        $seo = PrivatePageSeo::make(__('auth.reset_password'), $request->url());

        return view('auth.reset-password', [
            'seo' => $seo,
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function store(Request $request, string $locale): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
                event(new PasswordReset($user));
            },
        );

        return $status === Password::PasswordReset
            ? redirect()->route('login', ['locale' => $locale])->with('status', __('auth.password_reset'))
            : back()->withErrors(['email' => __($status)]);
    }
}
