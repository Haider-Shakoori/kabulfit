<?php

namespace App\Services\Accounts;

use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AddressService
{
    public function create(User $user, array $attributes): Address
    {
        return DB::transaction(function () use ($user, $attributes): Address {
            $makeDefault = (bool) ($attributes['is_default'] ?? false)
                || ! $user->addresses()->exists();

            if ($makeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            return $user->addresses()->create([
                ...$attributes,
                'uuid' => (string) Str::uuid(),
                'is_default' => $makeDefault,
            ]);
        });
    }

    public function update(User $user, Address $address, array $attributes): Address
    {
        abort_unless($address->user_id === $user->id, 404);

        return DB::transaction(function () use ($user, $address, $attributes): Address {
            if (! empty($attributes['is_default'])) {
                $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            }

            $address->update($attributes);

            if (! $user->addresses()->where('is_default', true)->exists()) {
                $address->update(['is_default' => true]);
            }

            return $address->refresh();
        });
    }

    public function delete(User $user, Address $address): void
    {
        abort_unless($address->user_id === $user->id, 404);

        DB::transaction(function () use ($user, $address): void {
            $wasDefault = $address->is_default;
            $address->delete();

            if ($wasDefault) {
                $user->addresses()->orderBy('id')->first()?->update(['is_default' => true]);
            }
        });
    }
}
