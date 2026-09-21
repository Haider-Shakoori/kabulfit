<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'preferred_locale' => $this->preferredLocale(),
            'email_verified' => $this->hasVerifiedEmail(),
            'addresses' => AddressResource::collection($this->whenLoaded('addresses')),
            'devices' => $this->whenLoaded('devices', fn () => $this->devices
                ->map(fn ($device) => [
                    'uuid' => $device->uuid,
                    'name' => $device->name,
                    'platform' => $device->platform,
                    'app_version' => $device->app_version,
                    'last_seen_at' => $device->last_seen_at?->toIso8601String(),
                    'revoked' => $device->revoked_at !== null,
                ])
                ->values()),
        ];
    }
}
