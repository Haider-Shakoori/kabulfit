<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShippingRate;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShippingRateController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        return view('admin.shipping-rates.index', [
            'rates' => ShippingRate::query()->latest()->get(),
            'seo' => PrivatePageSeo::make('Admin Shipping Rates', route('admin.shipping-rates.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $data = $this->validated($request);
        $rate = ShippingRate::query()->create([
            'weight_ranges' => $this->ranges($data['ranges']),
            'effective_date' => $data['effective_date'] ?? now()->toDateString(),
            'is_active' => true,
            'notes' => $data['notes'] ?? null,
        ]);

        $audit->record($request->user(), 'shipping_rate.created', $rate, ['shipping_rate_id' => $rate->id]);

        return back()->with('status', 'Shipping rate added.');
    }

    public function toggle(Request $request, string $locale, ShippingRate $shippingRate, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $shippingRate->update(['is_active' => ! $shippingRate->is_active]);
        $audit->record($request->user(), 'shipping_rate.status_changed', $shippingRate, [
            'shipping_rate_id' => $shippingRate->id,
            'is_active' => $shippingRate->is_active,
        ]);

        return back()->with('status', 'Shipping rate status updated.');
    }

    public function destroy(Request $request, string $locale, ShippingRate $shippingRate, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $audit->record($request->user(), 'shipping_rate.deleted', $shippingRate, ['shipping_rate_id' => $shippingRate->id]);
        $shippingRate->delete();

        return back()->with('status', 'Shipping rate deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'ranges' => ['required', 'array', 'min:1', 'max:20'],
            'ranges.*.min_kg' => ['required', 'numeric', 'min:0', 'max:1000'],
            'ranges.*.max_kg' => ['required', 'numeric', 'gt:ranges.*.min_kg', 'max:1000'],
            'ranges.*.price' => ['required', 'numeric', 'min:0', 'max:100000'],
            'effective_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function ranges(array $ranges): array
    {
        return collect($ranges)
            ->map(fn (array $range): array => [
                'min_kg' => round((float) $range['min_kg'], 2),
                'max_kg' => round((float) $range['max_kg'], 2),
                'price' => round((float) $range['price'], 2),
            ])
            ->values()
            ->all();
    }
}
