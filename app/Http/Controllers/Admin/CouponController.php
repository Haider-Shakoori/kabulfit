<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CouponController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        return view('admin.coupons.index', [
            'coupons' => Coupon::query()->latest()->get(),
            'seo' => PrivatePageSeo::make('Admin Discount Codes', route('admin.coupons.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function store(Request $request, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $coupon = Coupon::query()->create($this->payload($request));
        $audit->record($request->user(), 'coupon.created', $coupon, ['coupon_id' => $coupon->id]);

        return back()->with('status', 'Discount code created.');
    }

    public function update(Request $request, string $locale, Coupon $coupon, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $before = $coupon->getAttributes();
        $coupon->update($this->payload($request, $coupon));

        $audit->record($request->user(), 'coupon.updated', $coupon, [
            'coupon_id' => $coupon->id,
            'before' => $before,
            'after' => $coupon->fresh()->getAttributes(),
        ]);

        return back()->with('status', 'Discount code updated.');
    }

    public function destroy(Request $request, string $locale, Coupon $coupon, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('settings.manage'), 403);

        $audit->record($request->user(), 'coupon.deleted', $coupon, ['coupon_id' => $coupon->id]);
        $coupon->delete();

        return back()->with('status', 'Discount code deleted.');
    }

    private function payload(Request $request, ?Coupon $coupon = null): array
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:80', Rule::unique('coupons', 'code')->ignore($coupon?->id)],
            'type' => ['required', Rule::in(['percent', 'fixed'])],
            'value' => ['required', 'numeric', 'min:0.01', 'max:1000000'],
            'minimum_order' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'maximum_discount' => ['nullable', 'numeric', 'min:0', 'max:1000000'],
            'usage_limit' => ['nullable', 'integer', 'min:1', 'max:10000000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($data['type'] === 'percent' && (float) $data['value'] > 100) {
            return back()->withErrors(['value' => 'Percentage discounts cannot exceed 100%.'])->withInput()->throwResponse();
        }

        return [
            'code' => mb_strtoupper(trim($data['code'])),
            'type' => $data['type'],
            'value' => $data['type'] === 'percent'
                ? (int) round((float) $data['value'])
                : (int) round((float) $data['value'] * 100),
            'minimum_subtotal_minor' => (int) round((float) ($data['minimum_order'] ?? 0) * 100),
            'maximum_discount_minor' => filled($data['maximum_discount'] ?? null)
                ? (int) round((float) $data['maximum_discount'] * 100)
                : null,
            'usage_limit' => $data['usage_limit'] ?? null,
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];
    }
}
