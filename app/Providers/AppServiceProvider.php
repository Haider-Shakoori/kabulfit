<?php

namespace App\Providers;

use App\Contracts\Payments\PaymentGateway;
use App\Models\AuditLog;
use App\Models\MeasurementDefinition;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Role;
use App\Models\Setting;
use App\Models\TailoringRequest;
use App\Models\User;
use App\Policies\AuditLogPolicy;
use App\Policies\MeasurementDefinitionPolicy;
use App\Policies\OrderPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RolePolicy;
use App\Policies\SettingPolicy;
use App\Policies\TailoringRequestPolicy;
use App\Policies\UserPolicy;
use App\Services\Payments\StripePaymentGateway;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGateway::class, StripePaymentGateway::class);
    }

    public function boot(): void
    {
        Model::shouldBeStrict(! app()->isProduction());

        Gate::before(fn (User $user) => $user->hasRole('super-admin') ? true : null);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(MeasurementDefinition::class, MeasurementDefinitionPolicy::class);
        Gate::policy(TailoringRequest::class, TailoringRequestPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(AuditLog::class, AuditLogPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);

        RateLimiter::for('catalog-api', fn (Request $request) => Limit::perMinute(120)
            ->by($request->ip() ?: 'unknown'));

        RateLimiter::for('mobile-auth', function (Request $request): array {
            $email = Str::lower((string) $request->input('email', 'guest'));
            $ip = $request->ip() ?: 'unknown';

            return [
                Limit::perMinute(30)->by('auth-ip:'.$ip),
                Limit::perMinute(10)->by('auth-account:'.$email.'|'.$ip),
            ];
        });

        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(5)
            ->by('password-reset:'.($request->ip() ?: 'unknown')));

        RateLimiter::for('account-api', fn (Request $request) => Limit::perMinute(120)
            ->by('account:'.($request->user()?->getAuthIdentifier() ?? $request->ip() ?? 'unknown')));

        VerifyEmail::createUrlUsing(function (object $notifiable): string {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'locale' => $notifiable->preferredLocale(),
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ],
            );
        });

        ResetPassword::createUrlUsing(fn (object $notifiable, string $token): string => route(
            'password.reset',
            [
                'locale' => $notifiable->preferredLocale(),
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ],
        ));
    }
}
