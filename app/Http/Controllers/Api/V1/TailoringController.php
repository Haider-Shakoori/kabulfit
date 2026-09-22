<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MeasurementProfile;
use App\Models\Product;
use App\Models\TailoringRequest;
use App\Services\Measurements\TailoringService;
use App\Support\Measurements\MeasurementConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TailoringController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $requests = TailoringRequest::query()
            ->where('user_id', $request->user()->id)
            ->with([
                'product.translations',
                'variant',
                'measurementProfile',
                'orderItem.order',
                'orderItem.measurements',
            ])
            ->latest()
            ->get();

        return response()->json([
            'data' => $requests->map(fn (TailoringRequest $tailoring) => $this->payload($tailoring)),
        ]);
    }

    public function show(Request $request, string $locale, string $tailoring): JsonResponse
    {
        return response()->json([
            'data' => $this->payload($this->ownedRequest($request, $tailoring), true),
        ]);
    }

    public function store(Request $request, string $locale, TailoringService $service): JsonResponse
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

    private function ownedRequest(Request $request, string $uuid): TailoringRequest
    {
        return TailoringRequest::query()
            ->where('user_id', $request->user()->id)
            ->where('uuid', $uuid)
            ->with([
                'product.translations',
                'variant',
                'measurementProfile.values.definition.translations',
                'orderItem.order',
                'orderItem.measurements',
            ])
            ->firstOrFail();
    }

    private function payload(TailoringRequest $tailoring, bool $includeMeasurements = false): array
    {
        $tailoring->loadMissing([
            'product.translations',
            'variant',
            'measurementProfile',
            'orderItem.order',
            'orderItem.measurements',
        ]);

        $orderItem = $tailoring->orderItem;
        $profile = $tailoring->measurementProfile;
        $measurements = [];

        if ($includeMeasurements) {
            if ($orderItem) {
                $measurements = $orderItem->measurements
                    ->map(fn ($measurement) => [
                        'code' => $measurement->code,
                        'name' => $measurement->name,
                        'value_cm' => (float) $measurement->value_cm,
                    ])
                    ->values();
            } elseif ($profile) {
                $profile->loadMissing('values.definition.translations');
                $measurements = $profile->values
                    ->map(fn ($value) => [
                        'code' => $value->definition->code,
                        'name' => $value->definition->translation()?->name,
                        'value_cm' => (float) $value->value_cm,
                        'display_value' => MeasurementConverter::fromCm($value->value_cm, $profile->display_unit),
                        'display_unit' => $profile->display_unit,
                    ])
                    ->values();
            }
        }

        return [
            'uuid' => $tailoring->uuid,
            'status' => $tailoring->status,
            'product' => [
                'slug' => $tailoring->product->translation()?->slug,
                'sku' => $tailoring->product->sku,
                'name' => $tailoring->product->translation()?->name,
            ],
            'variant_sku' => $tailoring->variant?->sku,
            'measurement_profile' => [
                'uuid' => $profile?->uuid,
                'name' => $orderItem?->measurement_profile_name ?? $profile?->name,
            ],
            'notes' => $orderItem?->tailoring_notes ?? $tailoring->customer_notes,
            'order' => $orderItem?->order ? [
                'uuid' => $orderItem->order->uuid,
                'number' => $orderItem->order->number,
                'status' => $orderItem->order->status,
                'payment_status' => $orderItem->order->payment_status,
            ] : null,
            'measurements' => $includeMeasurements ? $measurements : null,
            'created_at' => $tailoring->created_at?->toIso8601String(),
        ];
    }
}
