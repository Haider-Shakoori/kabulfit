<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements HasLocalePreference, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'preferred_locale',
        'is_active',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(UserDevice::class);
    }

    public function measurementProfiles(): HasMany
    {
        return $this->hasMany(MeasurementProfile::class);
    }

    public function tailoringRequests(): HasMany
    {
        return $this->hasMany(TailoringRequest::class);
    }

    public function preferredLocale(): string
    {
        return in_array($this->preferred_locale, config('kabulfit.supported_locales'), true)
            ? $this->preferred_locale
            : config('kabulfit.default_locale');
    }
}
