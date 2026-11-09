<?php

namespace App\Observers;

use App\Models\Business;
use App\Models\Permission;
use App\Models\Role;

class BusinessObserver
{
    public function created(Business $business): void
    {
        $administratorRole = Role::firstOrCreate(
            [
                'business_id' => $business->id,
                'slug' => 'administrator',
            ],
            [
                'name' => 'Administrador',
                'is_system' => true,
            ]
        );

        $permissionIds = Permission::query()
            ->pluck('id')
            ->all();

        $administratorRole
            ->permissions()
            ->sync($permissionIds);
    }
}
