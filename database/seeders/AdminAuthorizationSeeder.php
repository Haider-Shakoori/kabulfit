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
            'tailoring.work' => 'Use the future tailor workspace',
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
            'seo.home.title.en' => ['seo', 'KabulFit — Afghan Fashion & Custom Tailoring'],
            'seo.home.title.fa' => ['seo', 'کابل‌فیت — پوشاک افغانی و خیاطی سفارشی'],
            'seo.home.title.ps' => ['seo', 'کابل‌فټ — افغان فېشن او سفارشي خیاطي'],
            'seo.home.description.en' => ['seo', 'Discover Afghan clothing, custom tailoring and contemporary KabulFit designs.'],
            'seo.home.description.fa' => ['seo', 'پوشاک افغانی، خیاطی سفارشی و طرح‌های معاصر کابل‌فیت را ببینید.'],
            'seo.home.description.ps' => ['seo', 'افغان کالي، سفارشي خیاطي او د کابل‌فټ معاصر ډیزاینونه وپلټئ.'],
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
