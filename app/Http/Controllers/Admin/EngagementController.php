<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\NewsletterSubscriber;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EngagementController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('customers.manage'), 403);

        return view('admin.engagement.index', [
            'messages' => ContactMessage::query()->latest()->paginate(20, ['*'], 'messages_page'),
            'subscribers' => NewsletterSubscriber::query()->latest('subscribed_at')->paginate(20, ['*'], 'subscribers_page'),
            'counts' => [
                'new_messages' => ContactMessage::query()->where('status', 'new')->count(),
                'active_subscribers' => NewsletterSubscriber::query()->where('is_active', true)->count(),
            ],
            'seo' => PrivatePageSeo::make('Admin Engagement', route('admin.engagement.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function messageStatus(Request $request, string $locale, ContactMessage $contactMessage, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('customers.manage'), 403);

        $data = $request->validate([
            'status' => ['required', Rule::in(['new', 'read', 'closed'])],
        ]);

        $contactMessage->update(['status' => $data['status']]);
        $audit->record($request->user(), 'contact_message.status_changed', $contactMessage, [
            'contact_message_id' => $contactMessage->id,
            'status' => $data['status'],
        ]);

        return back()->with('status', 'Contact message status updated.');
    }

    public function subscriberToggle(Request $request, string $locale, NewsletterSubscriber $subscriber, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('customers.manage'), 403);

        $subscriber->update([
            'is_active' => ! $subscriber->is_active,
            'subscribed_at' => ! $subscriber->is_active ? now() : $subscriber->subscribed_at,
            'unsubscribed_at' => $subscriber->is_active ? now() : null,
        ]);

        $audit->record($request->user(), 'newsletter_subscriber.status_changed', $subscriber, [
            'subscriber_id' => $subscriber->id,
            'is_active' => $subscriber->is_active,
        ]);

        return back()->with('status', 'Newsletter subscriber status updated.');
    }
}
