<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MeasurementProfile;
use App\Models\Product;
use App\Services\Measurements\TailoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TailoringController extends Controller
{
    public function store(Request $request, TailoringService $service): JsonResponse
    {
        $data = $request->validate([
            'product_slug' => 'required|string',
            'variant_sku' => 'nullable|string',
            'measurement_profile_uuid' => 'required|uuid',
            'notes' => 'nullable|string|max:2000',
        ]);

        $product = Product::query()
            ->where('tailoring_enabled', true)
            ->whereHas('translations', fn ($query) => $query
                ->where('locale', app()->getLocale())
                ->where('slug', $data['product_slug']))
            ->with(['translations', 'variants.inventory'])
            ->firstOrFail();

        $profile = MeasurementProfile::query()
            ->where('user_id', $request->user()->id)
            ->where('uuid', $data['measurement_profile_uuid'])
            ->firstOrFail();

        $variant = ! empty($data['variant_sku'])
            ? $product->variants->firstWhere('sku', $data['variant_sku'])
            : null;

        if (! empty($data['variant_sku']) && ! $variant) {
            throw ValidationException::withMessages(['variant_sku' => __('commerce.invalid_item')]);
        }

        $result = $service->addToCart(
            $request->user(),
            $product,
            $variant,
            $profile,
            $data['notes'] ?? null,
        );

        $tailoring = $result['tailoring'];
        $cart = $result['cart'];
        $line = $cart->items->firstWhere('tailoring_request_id', $tailoring->id);

        return response()->json([
            'data' => [
                'uuid' => $tailoring->uuid,
                'status' => $tailoring->status,
                'cart_uuid' => $cart->uuid,
                'cart_item_uuid' => $line?->uuid,
            ],
        ], 201);
    }
}
