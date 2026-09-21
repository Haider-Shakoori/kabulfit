<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\MobileLoginRequest;
use App\Http\Requests\Api\V1\MobileRegisterRequest;
use App\Http\Resources\AccountResource;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\Auth\MobileTokenService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\NewAccessToken;

class MobileAuthController extends Controller
{
    public function register(
        MobileRegisterRequest $request,
        MobileTokenService $tokens,
    ): JsonResponse {
        $user = User::query()->create($request->safe()->only([
            'name', 'email', 'phone', 'preferred_locale', 'password',
        ]));

        event(new Registered($user));

        return $this->tokenResponse(
            $user,
            $tokens->issue($user, $request->validated()),
            201,
        );
    }

    public function login(
        MobileLoginRequest $request,
        MobileTokenService $tokens,
    ): JsonResponse {
        $user = User::query()
            ->where('email', $request->validated('email'))
            ->first();

        if ($user === null || ! Hash::check($request->validated('password'), $user->password)) {
            throw ValidationException::withMessages(['email' => [__('auth.failed')]]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages(['email' => [__('auth.inactive')]]);
        }

        $user->update(['preferred_locale' => $request->route('locale')]);

        return $this->tokenResponse($user, $tokens->issue($user, $request->validated()));
    }

    public function me(Request $request): AccountResource
    {
        $user = $request->user();
        $tokenName = $user->currentAccessToken()?->name;

        if (is_string($tokenName) && str_starts_with($tokenName, 'mobile:')) {
            $user->devices()
                ->where('uuid', substr($tokenName, 7))
                ->update(['last_seen_at' => now()]);
        }

        return new AccountResource($user->load([
            'addresses' => fn ($query) => $query->orderByDesc('is_default')->orderBy('id'),
            'devices' => fn ($query) => $query->orderByDesc('last_seen_at'),
        ]));
    }

    public function logout(Request $request, MobileTokenService $tokens): JsonResponse
    {
        $tokens->revokeCurrent($request->user());

        return response()->json(['message' => __('auth.logged_out')]);
    }

    public function revokeDevice(
        Request $request,
        string $locale,
        UserDevice $device,
        MobileTokenService $tokens,
    ): JsonResponse {
        $tokens->revokeDevice($request->user(), $device);

        return response()->json(['message' => __('auth.device_revoked')]);
    }

    private function tokenResponse(User $user, NewAccessToken $token, int $status = 200): JsonResponse
    {
        return response()->json([
            'data' => [
                'token' => $token->plainTextToken,
                'token_type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at?->toIso8601String(),
                'user' => (new AccountResource($user))->resolve(),
            ],
            'error' => null,
        ], $status);
    }
}
