<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Services\Admin\AuditService;
use App\Support\Seo\PrivatePageSeo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', Role::class);

        return view('admin.roles.index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get(),
            'permissions' => Permission::query()->orderBy('slug')->get(),
            'seo' => PrivatePageSeo::make('Admin Roles', route('admin.roles.index', ['locale' => app()->getLocale()])),
        ]);
    }

    public function update(Request $request, string $locale, Role $role, AuditService $audit): RedirectResponse
    {
        $this->authorize('update', $role);

        if ($role->slug === 'super-admin') {
            throw ValidationException::withMessages(['role' => 'The super-admin permission set is protected.']);
        }

        $data = $request->validate(['permissions' => 'array', 'permissions.*' => 'string|exists:permissions,slug']);
        $before = $role->permissions()->pluck('slug')->sort()->values()->all();
        $role->permissions()->sync(Permission::query()->whereIn('slug', $data['permissions'] ?? [])->pluck('id'));
        $after = $role->permissions()->pluck('slug')->sort()->values()->all();

        $audit->record($request->user(), 'role.permissions_updated', $role, ['role' => $role->slug, 'before' => $before, 'after' => $after]);

        return back()->with('status', 'Role permissions updated.');
    }
}
