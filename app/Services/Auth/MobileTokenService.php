<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserDevice;
use Laravel\Sanctum\NewAccessToken;

class MobileTokenService
{
    public function issue(User $user, array $device): NewAccessToken
    {
        $uuid = $device['device_uuid'];
        $tokenName = 'mobile:'.$uuid;

        $user->tokens()->where('name', $tokenName)->delete();

        $user->devices()->updateOrCreate(
            ['uuid' => $uuid],
            [
                'name' => $device['device_name'],
                'platform' => $device['platform'],
                'app_version' => $device['app_version'] ?? null,
                'last_seen_at' => now(),
                'revoked_at' => null,
            ],
        );

        return $user->createToken(
            $tokenName,
            ['mobile'],
            now()->addDays(config('kabulfit.auth.mobile_token_days')),
        );
    }

    public function revokeCurrent(User $user): void
    {
        $token = $user->currentAccessToken();

        if ($token === null) {
            return;
        }

        $uuid = str_starts_with($token->name, 'mobile:')
            ? substr($token->name, 7)
            : null;

        $token->delete();

        if ($uuid !== null) {
            $user->devices()->where('uuid', $uuid)->update(['revoked_at' => now()]);
        }
    }

    public function revokeDevice(User $user, UserDevice $device): void
    {
        abort_unless($device->user_id === $user->id, 404);

        $user->tokens()->where('name', 'mobile:'.$device->uuid)->delete();
        $device->update(['revoked_at' => now()]);
    }
}
