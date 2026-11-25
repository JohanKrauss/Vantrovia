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
            [
                'module' => 'users',
                'name' => 'Asignar sucursales',
                'slug' => 'users.branches',
                'description' => 'Puede administrar las sucursales permitidas para los usuarios.',
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

            // Categorías
            [
                'module' => 'categories',
                'name' => 'Ver categorías',
                'slug' => 'categories.view',
                'description' => 'Puede consultar categorías de productos.',
            ],
            [
                'module' => 'categories',
                'name' => 'Crear categorías',
                'slug' => 'categories.create',
                'description' => 'Puede crear categorías de productos.',
            ],
            [
                'module' => 'categories',
                'name' => 'Editar categorías',
                'slug' => 'categories.update',
                'description' => 'Puede modificar categorías de productos.',
            ],
            [
                'module' => 'categories',
                'name' => 'Cambiar estado de categorías',
                'slug' => 'categories.status',
                'description' => 'Puede activar o desactivar categorías.',
            ],

            // Unidades
            [
                'module' => 'units',
                'name' => 'Ver unidades',
                'slug' => 'units.view',
                'description' => 'Puede consultar unidades de medida.',
            ],
            [
                'module' => 'units',
                'name' => 'Crear unidades',
                'slug' => 'units.create',
                'description' => 'Puede crear unidades de medida.',
            ],
            [
                'module' => 'units',
                'name' => 'Editar unidades',
                'slug' => 'units.update',
                'description' => 'Puede modificar unidades de medida.',
            ],
            [
                'module' => 'units',
                'name' => 'Cambiar estado de unidades',
                'slug' => 'units.status',
                'description' => 'Puede activar o desactivar unidades de medida.',
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

            // Productos
            [
                'module' => 'products',
                'name' => 'Ver productos',
                'slug' => 'products.view',
                'description' => 'Puede consultar productos del negocio.',
            ],
            [
                'module' => 'products',
                'name' => 'Crear productos',
                'slug' => 'products.create',
                'description' => 'Puede crear productos.',
            ],
            [
                'module' => 'products',
                'name' => 'Editar productos',
                'slug' => 'products.update',
                'description' => 'Puede modificar productos.',
            ],
            [
                'module' => 'products',
                'name' => 'Cambiar estado de productos',
                'slug' => 'products.status',
                'description' => 'Puede activar o desactivar productos.',
            ],

            // Inventario
            [
                'module' => 'inventory',
                'name' => 'Ver inventario',
                'slug' => 'inventory.view',
                'description' => 'Puede consultar el inventario de la sucursal.',
            ],
            [
                'module' => 'inventory',
                'name' => 'Ajustar inventario',
                'slug' => 'inventory.adjust',
                'description' => 'Puede realizar entradas y salidas manuales de inventario.',
            ],
            [
                'module' => 'inventory',
                'name' => 'Configurar stock mínimo',
                'slug' => 'inventory.minimum-stock',
                'description' => 'Puede configurar el stock mínimo de los productos.',
            ],
            [
                'module' => 'inventory',
                'name' => 'Ver movimientos de inventario',
                'slug' => 'inventory.movements',
                'description' => 'Puede consultar el historial de movimientos de inventario.',
            ],

            // Traspasos de inventario
            [
                'module' => 'inventory',
                'name' => 'Ver traspasos',
                'slug' => 'inventory.transfers.view',
                'description' => 'Puede consultar los traspasos entre sucursales.',
            ],
            [
                'module' => 'inventory',
                'name' => 'Crear traspasos',
                'slug' => 'inventory.transfers.create',
                'description' => 'Puede transferir existencias entre sucursales.',
            ],

            // Proveedores
            [
                'module' => 'suppliers',
                'name' => 'Ver proveedores',
                'slug' => 'suppliers.view',
                'description' => 'Puede consultar los proveedores del negocio.',
            ],
            [
                'module' => 'suppliers',
                'name' => 'Crear proveedores',
                'slug' => 'suppliers.create',
                'description' => 'Puede crear proveedores.',
            ],
            [
                'module' => 'suppliers',
                'name' => 'Editar proveedores',
                'slug' => 'suppliers.update',
                'description' => 'Puede modificar proveedores.',
            ],
            [
                'module' => 'suppliers',
                'name' => 'Cambiar estado de proveedores',
                'slug' => 'suppliers.status',
                'description' => 'Puede activar o desactivar proveedores.',
            ],

            // Compras
            [
                'module' => 'purchases',
                'name' => 'Ver compras',
                'slug' => 'purchases.view',
                'description' => 'Puede consultar las compras del negocio.',
            ],
            [
                'module' => 'purchases',
                'name' => 'Registrar compras',
                'slug' => 'purchases.create',
                'description' => 'Puede registrar compras e ingresar existencias al inventario.',
            ],
        ];

        $permissionIds = collect();

        foreach ($permissions as $permission) {
            $model = Permission::updateOrCreate(
                [
                    'slug' => $permission['slug'],
                ],
                $permission
            );

            $permissionIds->push($model->id);
        }

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
                    $permissionIds->all()
                );
            });
    }
}
