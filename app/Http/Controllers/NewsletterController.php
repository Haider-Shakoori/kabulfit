<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class NewsletterController extends Controller
{
    public function subscribe(Request $request, string $locale): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with('newsletter_status', __('site.newsletter_success'));
        }

        $data = $request->validate([
            'email' => ['required', 'email:rfc', 'max:254'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::query()->updateOrCreate(
            ['email' => mb_strtolower(trim($data['email']))],
            [
                'locale' => app()->getLocale(),
                'is_active' => true,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        $unsubscribeUrl = URL::temporarySignedRoute(
            'newsletter.unsubscribe',
            now()->addYears(5),
            ['locale' => app()->getLocale(), 'subscriber' => $subscriber],
        );

        rescue(function () use ($subscriber, $unsubscribeUrl): void {
            Mail::raw(
                __('site.newsletter_email_body', ['url' => $unsubscribeUrl]),
                function ($message) use ($subscriber): void {
                    $message->to($subscriber->email)
                        ->subject(__('site.newsletter_email_subject'));
                },
            );
        }, report: false);

        return back()->with('newsletter_status', __('site.newsletter_success'));
    }

    public function unsubscribe(Request $request, string $locale, NewsletterSubscriber $subscriber): RedirectResponse
    {
        abort_unless($request->hasValidSignature(), 403);

        $subscriber->update([
            'is_active' => false,
            'unsubscribed_at' => now(),
        ]);

        return redirect()->route('home', ['locale' => app()->getLocale()])
            ->with('newsletter_status', __('site.newsletter_unsubscribed'));
    }
}
