<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AdminAuthorizationSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'admin.access' => 'Access administration',
            'dashboard.view' => 'View admin dashboard',
            'products.manage' => 'Manage catalog products',
            'orders.manage' => 'Manage orders and shipments',
            'customers.manage' => 'Manage customer accounts',
            'measurements.manage' => 'Manage measurement definitions',
            'tailoring.manage' => 'Manage tailoring requests',
            'payments.view' => 'View Stripe payments and payment events',
            'payments.refund' => 'Refund eligible Stripe payments',
            'settings.manage' => 'Manage site and SEO settings',
            'audit.view' => 'View immutable audit logs',
            'roles.manage' => 'Manage user roles and role permissions',
            'tailoring.work' => 'Use tailor workspace',
        ];

        foreach ($permissions as $slug => $name) {
            Permission::query()->updateOrCreate(['slug' => $slug], ['name' => $name]);
        }

        $roleMap = [
            'super-admin' => array_keys($permissions),
            'administrator' => array_keys($permissions),
            'operations' => [
                'admin.access', 'dashboard.view', 'orders.manage', 'customers.manage',
                'tailoring.manage', 'payments.view',
            ],
            'catalog-manager' => [
                'admin.access', 'dashboard.view', 'products.manage', 'measurements.manage',
            ],
            'support' => [
                'admin.access', 'dashboard.view', 'orders.manage', 'customers.manage',
                'tailoring.manage', 'payments.view',
            ],
            'tailor' => ['tailoring.work'],
        ];

        foreach ($roleMap as $slug => $permissionSlugs) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'uuid' => Role::query()->where('slug', $slug)->value('uuid') ?? (string) Str::uuid(),
                    'name' => Str::headline($slug),
                    'is_system' => true,
                ],
            );

            $role->permissions()->sync(
                Permission::query()->whereIn('slug', $permissionSlugs)->pluck('id'),
            );
        }

        foreach ([
            'seo.home.title.en' => ['seo', 'Authentic Afghan Clothes & Custom Tailoring | KabulFit'],
            'seo.home.title.fa' => ['seo', 'لباس اصیل افغانی و خیاطی سفارشی | کابل‌فیت'],
            'seo.home.title.ps' => ['seo', 'اصلي افغان جامې او سفارشي خیاطي | کابل‌فټ'],
            'seo.home.description.en' => ['seo', 'Discover authentic Afghan clothing, Perahan Tunban, embroidered dresses and made-to-measure tailoring from KabulFit.'],
            'seo.home.description.fa' => ['seo', 'لباس‌های اصیل افغانی، پیرهن تنبان، لباس‌های خامک‌دوزی‌شده و خیاطی سفارشی را در کابل‌فیت کشف کنید.'],
            'seo.home.description.ps' => ['seo', 'په کابل‌فټ کې اصلي افغان جامې، پیرهن تنبان، ګنډل شوي لباسونه او د اندازې مطابق خیاطي ومومئ.'],
            'site.contact_email' => ['general', 'info@kabulfit.com'],
        ] as $key => [$group, $value]) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'uuid' => Setting::query()->where('key', $key)->value('uuid') ?? (string) Str::uuid(),
                    'group' => $group,
                    'value' => $value,
                ],
            );
        }
    }
}
