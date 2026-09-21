<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Services\Accounts\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class MobileAddressController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return AddressResource::collection(
            $request->user()->addresses()
                ->orderByDesc('is_default')
                ->orderBy('id')
                ->get(),
        );
    }

    public function store(AddressRequest $request, AddressService $addresses): JsonResponse
    {
        return (new AddressResource(
            $addresses->create($request->user(), $request->validated()),
        ))->response()->setStatusCode(201);
    }

    public function update(
        AddressRequest $request,
        string $locale,
        Address $address,
        AddressService $addresses,
    ): AddressResource {
        return new AddressResource(
            $addresses->update($request->user(), $address, $request->validated()),
        );
    }

    public function destroy(
        Request $request,
        string $locale,
        Address $address,
        AddressService $addresses,
    ): JsonResponse {
        $addresses->delete($request->user(), $address);

        return response()->json(null, 204);
    }
}
