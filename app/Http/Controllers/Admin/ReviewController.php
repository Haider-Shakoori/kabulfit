<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        $status = (string) $request->query('status', 'all');

        $reviews = Review::query()
            ->with(['user:id,name,email', 'product.translations'])
            ->when($status === 'approved', fn ($query) => $query->where('is_approved', true))
            ->when($status === 'hidden', fn ($query) => $query->where('is_approved', false))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'status' => in_array($status, ['all', 'approved', 'hidden'], true) ? $status : 'all',
            'counts' => [
                'all' => Review::query()->count(),
                'approved' => Review::query()->where('is_approved', true)->count(),
                'hidden' => Review::query()->where('is_approved', false)->count(),
            ],
            'seo' => PrivatePageSeo::make('Admin Reviews', route('admin.reviews.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function toggle(Request $request, string $locale, Review $review, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        $review->update(['is_approved' => ! $review->is_approved]);
        $audit->record($request->user(), 'review.approval_changed', $review, [
            'review_id' => $review->id,
            'is_approved' => $review->is_approved,
        ]);

        return back()->with('status', $review->is_approved ? 'Review approved.' : 'Review hidden.');
    }

    public function destroy(Request $request, string $locale, Review $review, AuditService $audit): RedirectResponse
    {
        abort_unless($request->user()->hasPermission('products.manage'), 403);

        $audit->record($request->user(), 'review.deleted', $review, [
            'review_id' => $review->id,
            'product_id' => $review->product_id,
            'user_id' => $review->user_id,
        ]);
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}
