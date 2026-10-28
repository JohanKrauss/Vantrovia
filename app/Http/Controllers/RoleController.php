<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    public function index(Business $business)
    {
        $roles = $business
            ->roles()
            ->withCount('permissions')
            ->orderByDesc('is_system')
            ->orderBy('name')
            ->get();

        return view(
            'businesses.roles.index',
            compact('business', 'roles')
        );
    }

    public function create(Business $business)
    {
        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view(
            'businesses.roles.create',
            compact(
                'business',
                'permissions'
            )
        );
    }

    public function store(
        Request $request,
        Business $business
    ) {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        $slug = empty($data['slug'])
            ? $this->generateUniqueSlug(
                $business,
                $data['name']
            )
            : Str::lower($data['slug']);

        $exists = Role::query()
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'slug',
                $slug
            )
            ->exists();

        if ($exists) {
            return back()
                ->withErrors([
                    'slug' => 'Ya existe un rol con ese identificador.',
                ])
                ->withInput();
        }

        DB::transaction(function () use (
            $business,
            $data,
            $slug
        ) {
            $role = $business->roles()->create([
                'name' => $data['name'],
                'slug' => $slug,
                'is_system' => false,
            ]);

            $role->permissions()->sync(
                $data['permissions'] ?? []
            );
        });

        return redirect()
            ->route(
                'businesses.roles.index',
                $business
            )
            ->with(
                'success',
                'Rol creado correctamente.'
            );
    }

    public function edit(
        Business $business,
        Role $role
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $role
        );

        $role->load('permissions');

        $permissions = Permission::query()
            ->orderBy('module')
            ->orderBy('name')
            ->get()
            ->groupBy('module');

        return view(
            'businesses.roles.edit',
            compact(
                'business',
                'role',
                'permissions'
            )
        );
    }

    public function update(
        Request $request,
        Business $business,
        Role $role
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $role
        );

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',

                Rule::unique(
                    'roles',
                    'slug'
                )
                    ->where(
                        fn($query) =>
                        $query->where(
                            'business_id',
                            $business->id
                        )
                    )
                    ->ignore($role->id),
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'integer',
                'exists:permissions,id',
            ],
        ]);

        DB::transaction(function () use (
            $role,
            $data
        ) {
            $role->update([
                'name' => $data['name'],
                'slug' => Str::lower(
                    $data['slug']
                ),
            ]);

            $role->permissions()->sync(
                $data['permissions'] ?? []
            );
        });

        return redirect()
            ->route(
                'businesses.roles.index',
                $business
            )
            ->with(
                'success',
                'Rol actualizado correctamente.'
            );
    }

    public function destroy(
        Business $business,
        Role $role
    ) {
        $this->ensureBelongsToBusiness(
            $business,
            $role
        );

        if ($role->is_system) {
            return back()->with(
                'warning',
                'Los roles del sistema no pueden eliminarse.'
            );
        }

        $inUse = DB::table('business_user')
            ->where(
                'business_id',
                $business->id
            )
            ->where(
                'role_id',
                $role->id
            )
            ->exists();

        if ($inUse) {
            return back()->with(
                'warning',
                'No puedes eliminar un rol que está asignado a usuarios.'
            );
        }

        $role->delete();

        return back()->with(
            'success',
            'Rol eliminado correctamente.'
        );
    }

    private function ensureBelongsToBusiness(
        Business $business,
        Role $role
    ): void {
        abort_unless(
            $role->business_id === $business->id,
            404
        );
    }

    private function generateUniqueSlug(
        Business $business,
        string $name
    ): string {
        $base = Str::slug($name);

        if (empty($base)) {
            $base = 'rol';
        }

        $base = substr(
            $base,
            0,
            80
        );

        $slug = $base;
        $counter = 2;

        while (
            Role::where(
                'business_id',
                $business->id
            )
            ->where('slug', $slug)
            ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
