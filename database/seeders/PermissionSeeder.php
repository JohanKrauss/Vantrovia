<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [

            // Dashboard
            [
                'module' => 'dashboard',
                'name' => 'Ver dashboard',
                'slug' => 'dashboard.view',
                'description' => 'Puede acceder al dashboard del negocio.',
            ],

            // Usuarios
            [
                'module' => 'users',
                'name' => 'Ver usuarios',
                'slug' => 'users.view',
                'description' => 'Puede consultar usuarios del negocio.',
            ],
            [
                'module' => 'users',
                'name' => 'Asignar usuarios',
                'slug' => 'users.assign',
                'description' => 'Puede asignar usuarios al negocio.',
            ],
            [
                'module' => 'users',
                'name' => 'Editar usuarios',
                'slug' => 'users.update',
                'description' => 'Puede modificar usuarios.',
            ],
            [
                'module' => 'users',
                'name' => 'Activar o desactivar usuarios',
                'slug' => 'users.status',
                'description' => 'Puede modificar el estado de usuarios.',
            ],

            // Sucursales
            [
                'module' => 'branches',
                'name' => 'Ver sucursales',
                'slug' => 'branches.view',
                'description' => 'Puede consultar sucursales.',
            ],
            [
                'module' => 'branches',
                'name' => 'Crear sucursales',
                'slug' => 'branches.create',
                'description' => 'Puede crear sucursales.',
            ],
            [
                'module' => 'branches',
                'name' => 'Editar sucursales',
                'slug' => 'branches.update',
                'description' => 'Puede modificar sucursales.',
            ],
            [
                'module' => 'branches',
                'name' => 'Cambiar estado de sucursales',
                'slug' => 'branches.status',
                'description' => 'Puede activar o desactivar sucursales.',
            ],

            // Roles
            [
                'module' => 'roles',
                'name' => 'Ver roles',
                'slug' => 'roles.view',
                'description' => 'Puede consultar roles.',
            ],
            [
                'module' => 'roles',
                'name' => 'Crear roles',
                'slug' => 'roles.create',
                'description' => 'Puede crear roles.',
            ],
            [
                'module' => 'roles',
                'name' => 'Editar roles',
                'slug' => 'roles.update',
                'description' => 'Puede modificar roles y permisos.',
            ],
            [
                'module' => 'roles',
                'name' => 'Eliminar roles',
                'slug' => 'roles.delete',
                'description' => 'Puede eliminar roles no utilizados.',
            ],
            [
                'module' => 'roles',
                'name' => 'Asignar roles',
                'slug' => 'roles.assign',
                'description' => 'Puede asignar roles a usuarios.',
            ],

            // Negocio
            [
                'module' => 'business',
                'name' => 'Ver configuración del negocio',
                'slug' => 'business.view',
                'description' => 'Puede consultar datos del negocio.',
            ],
            [
                'module' => 'business',
                'name' => 'Editar configuración del negocio',
                'slug' => 'business.update',
                'description' => 'Puede modificar datos del negocio.',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                $permission
            );
        }

        $permissionIds = Permission::pluck('id');

        Business::query()
            ->each(function (Business $business) use ($permissionIds) {

                $role = Role::firstOrCreate(
                    [
                        'business_id' => $business->id,
                        'slug' => 'administrator',
                    ],
                    [
                        'name' => 'Administrador',
                        'is_system' => true,
                    ]
                );

                $role->permissions()->sync(
                    $permissionIds
                );
            });
    }
}
