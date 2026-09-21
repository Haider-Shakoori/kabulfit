<?php

namespace App\Http\Controllers;

use App\Models\MeasurementProfile;
use App\Models\Product;
use App\Services\Measurements\TailoringService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TailoringController extends Controller
{
    public function create(Request $request, string $slug): View
    {
        $product = $this->product($slug);
        $profiles = $request->user()
            ->measurementProfiles()
            ->where('garment_type', $product->measurement_garment_type)
            ->with('values.definition.translations')
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get();

        return view('tailoring.create', [
            'product' => $product,
            'profiles' => $profiles,
            'seo' => PrivatePageSeo::make(
                __('measurements.tailor_this_outfit'),
                route('tailoring.create', ['locale' => app()->getLocale(), 'slug' => $slug]),
            ),
        ]);
    }

    public function store(Request $request, string $slug, TailoringService $service): RedirectResponse
    {
        $data = $request->validate([
            'variant_sku' => 'nullable|string',
            'measurement_profile_uuid' => 'required|uuid',
            'notes' => 'nullable|string|max:2000',
        ]);
        $product = $this->product($slug);
        $profile = MeasurementProfile::query()
            ->where('user_id', $request->user()->id)
            ->where('uuid', $data['measurement_profile_uuid'])
            ->firstOrFail();
        $variant = ! empty($data['variant_sku'])
            ? $product->variants->firstWhere('sku', $data['variant_sku'])
            : null;

        $service->addToCart(
            $request->user(),
            $product,
            $variant,
            $profile,
            $data['notes'] ?? null,
        );

        return redirect()
            ->route('cart', ['locale' => app()->getLocale()])
            ->with('status', __('measurements.tailoring_added'));
    }

    private function product(string $slug): Product
    {
        return Product::query()
            ->where('tailoring_enabled', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', app()->getLocale())
                ->where('slug', $slug))
            ->with(['translations', 'variants.inventory', 'variants.size', 'variants.color.translations'])
            ->firstOrFail();
    }
}
