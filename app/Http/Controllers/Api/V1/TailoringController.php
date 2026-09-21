<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MeasurementProfile;
use App\Models\Product;
use App\Models\TailoringRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TailoringController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_slug' => 'required|string',
            'variant_sku' => 'nullable|string',
            'measurement_profile_uuid' => 'required|uuid',
            'notes' => 'nullable|string|max:2000',
        ]);
        $product = Product::whereHas('translations', fn ($query) => $query->where('locale', app()->getLocale())->where('slug', $data['product_slug']))->with('variants')->firstOrFail();
        $profile = MeasurementProfile::where('user_id', $request->user()->id)->where('uuid', $data['measurement_profile_uuid'])->firstOrFail();
        $variant = ! empty($data['variant_sku']) ? $product->variants->firstWhere('sku', $data['variant_sku']) : null;
        if (! empty($data['variant_sku']) && ! $variant) {
            abort(422, __('commerce.invalid_item'));
        }

        $tailoring = TailoringRequest::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'product_id' => $product->id,
            'product_variant_id' => $variant?->id,
            'measurement_profile_id' => $profile->id,
            'status' => 'ready',
            'customer_notes' => $data['notes'] ?? null,
        ]);

        return response()->json(['data' => ['uuid' => $tailoring->uuid, 'status' => $tailoring->status]], 201);
    }
}
