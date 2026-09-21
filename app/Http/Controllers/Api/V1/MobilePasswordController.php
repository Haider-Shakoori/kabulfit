<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class MobilePasswordController extends Controller
{
    public function forgot(Request $request): JsonResponse
    {
        $validated = $request->validate(['email' => ['required', 'email:rfc']]);

        Password::sendResetLink(['email' => mb_strtolower(trim($validated['email']))]);

        return response()->json(['message' => __('auth.reset_link_sent')]);
    }

    public function reset(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email:rfc'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)->letters()->numbers()],
        ]);

        $status = Password::reset(
            $validated,
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                $user->tokens()->delete();
                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PasswordReset) {
            return response()->json([
                'message' => __($status),
                'errors' => ['email' => [__($status)]],
            ], 422);
        }

        return response()->json(['message' => __('auth.password_reset')]);
    }
}
