<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', User::class);

        return view('admin.customers.index', [
            'customers' => User::query()->with('roles')->latest()->paginate(30),
            'seo' => PrivatePageSeo::make('Admin Customers', route('admin.customers.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function show(string $locale, User $customer): View
    {
        $this->authorize('view', $customer);

        return view('admin.customers.show', [
            'customer' => $customer->load(['roles', 'addresses', 'orders']),
            'roles' => Role::query()->orderBy('name')->get(),
            'seo' => PrivatePageSeo::make('Admin Customer '.$customer->email, route('admin.customers.show', ['locale' => app()->getLocale(), 'customer' => $customer->uuid])),
        ]);
    }

    public function update(Request $request, string $locale, User $customer, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $customer);
        $data = $request->validate([
            'is_active' => 'nullable|boolean',
            'preferred_locale' => 'required|in:en,fa,ps',
        ]);

        if ($request->user()->is($customer) && ! ($data['is_active'] ?? false)) {
            throw ValidationException::withMessages(['is_active' => 'You cannot deactivate your own administrator account.']);
        }

        $before = $customer->only(['is_active', 'preferred_locale']);
        $customer->update(['is_active' => (bool) ($data['is_active'] ?? false), 'preferred_locale' => $data['preferred_locale']]);
        $audit->record($request->user(), 'customer.updated', $customer, ['customer_uuid' => $customer->uuid, 'before' => $before, 'after' => $customer->only(['is_active', 'preferred_locale'])]);

        return back()->with('status', 'Customer updated.');
    }

    public function roles(Request $request, string $locale, User $customer, AuditService $audit): RedirectResponse
    {
        $this->authorize('manageRoles', $customer);
        $data = $request->validate(['roles' => 'array', 'roles.*' => 'string|exists:roles,slug']);
        $requested = $data['roles'] ?? [];

        if (in_array('super-admin', $requested, true) && ! $request->user()->hasRole('super-admin')) {
            throw ValidationException::withMessages(['roles' => 'Only a super-admin can grant the super-admin role.']);
        }

        if ($request->user()->is($customer) && $customer->hasRole('super-admin') && ! in_array('super-admin', $requested, true)) {
            throw ValidationException::withMessages(['roles' => 'You cannot remove your own super-admin role.']);
        }

        $before = $customer->roles()->pluck('slug')->sort()->values()->all();
        $customer->roles()->sync(Role::query()->whereIn('slug', $requested)->pluck('id'));
        $after = $customer->roles()->pluck('slug')->sort()->values()->all();

        $audit->record($request->user(), 'customer.roles_updated', $customer, ['customer_uuid' => $customer->uuid, 'before' => $before, 'after' => $after]);

        return back()->with('status', 'Roles updated.');
    }
}
