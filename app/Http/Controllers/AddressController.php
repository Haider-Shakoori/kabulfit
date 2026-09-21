<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Services\Accounts\AddressService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function store(AddressRequest $request, string $locale, AddressService $addresses): RedirectResponse
    {
        $addresses->create($request->user(), $request->validated());

        return back()->with('status', __('account.address_saved'));
    }

    public function update(
        AddressRequest $request,
        string $locale,
        Address $address,
        AddressService $addresses,
    ): RedirectResponse {
        $addresses->update($request->user(), $address, $request->validated());

        return back()->with('status', __('account.address_saved'));
    }

    public function destroy(
        Request $request,
        string $locale,
        Address $address,
        AddressService $addresses,
    ): RedirectResponse {
        $addresses->delete($request->user(), $address);

        return back()->with('status', __('account.address_deleted'));
    }
}
