<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationController extends Controller
{
    public function notice(Request $request, string $locale): View|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account', ['locale' => $locale]);
        }

        $seo = PrivatePageSeo::make(__('auth.verify_email'), route('verification.notice', ['locale' => $locale]));

        return view('auth.verify-email', compact('seo'));
    }

    public function verify(EmailVerificationRequest $request, string $locale): RedirectResponse
    {
        $request->fulfill();

        return redirect()->route('account', ['locale' => $locale])->with('status', __('auth.email_verified'));
    }

    public function send(Request $request): RedirectResponse
    {
        if (! $request->user()->hasVerifiedEmail()) {
            $request->user()->sendEmailVerificationNotification();
        }

        return back()->with('status', __('auth.verification_sent'));
    }
}
