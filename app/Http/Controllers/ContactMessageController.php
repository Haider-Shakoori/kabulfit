<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Services\Settings\SiteSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactMessageController extends Controller
{
    public function store(Request $request, string $locale, SiteSettings $settings): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with('status', __('site.contact_sent'));
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'subject' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'min:5', 'max:5000'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        $contact = ContactMessage::query()->create([
            'name' => trim($data['name']),
            'email' => mb_strtolower(trim($data['email'])),
            'subject' => filled($data['subject'] ?? null) ? trim($data['subject']) : null,
            'message' => trim($data['message']),
            'status' => 'new',
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string) $request->userAgent(), 0, 500),
        ]);

        $recipient = (string) $settings->get('site.contact_email', 'info@kabulfit.com');

        rescue(function () use ($recipient, $contact): void {
            Mail::raw(
                "Name: {$contact->name}\nEmail: {$contact->email}\nSubject: ".($contact->subject ?: 'General Inquiry')."\n\nMessage:\n{$contact->message}",
                function ($message) use ($recipient, $contact): void {
                    $message->to($recipient)
                        ->replyTo($contact->email, $contact->name)
                        ->subject('KabulFit Contact: '.($contact->subject ?: 'General Inquiry'));
                },
            );
        }, report: false);

        return back()->with('status', __('site.contact_sent'));
    }
}
