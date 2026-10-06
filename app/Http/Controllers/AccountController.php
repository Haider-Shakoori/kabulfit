<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __invoke(Request $request, string $locale): View
    {
        $user = $request->user()->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->orderBy('id'),
            'devices' => fn ($query) => $query->orderByDesc('last_seen_at'),
            'roles',
        ]);

        $recentOrders = $user->orders()
            ->latest()
            ->limit(5)
            ->get();

        $stats = [
            'orders' => $user->orders()->count(),
            'wishlist' => Wishlist::query()->where('user_id', $user->id)->count(),
            'addresses' => $user->addresses->count(),
            'measurements' => $user->measurementProfiles()->count(),
        ];

        $seo = PrivatePageSeo::make(__('account.title'), route('account', ['locale' => $locale]));

        return view('account.index', compact('user', 'recentOrders', 'stats', 'seo'));
    }

    public function update(Request $request, string $locale): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:32'],
            'preferred_locale' => ['nullable', Rule::in(config('kabulfit.supported_locales'))],
        ]);

        $request->user()->update([
            'name' => $data['name'],
            'phone' => $data['phone'] ?? null,
            'preferred_locale' => $data['preferred_locale'] ?? $request->user()->preferred_locale,
        ]);

        return back()->with('status', __('account.profile_saved'));
    }
}
