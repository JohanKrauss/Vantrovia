<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            [
                'email' => 'admin@vantrovia.local',
            ],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('ChangeMe123!'),
                'is_active' => true,
            ]
        );

        $user->forceFill([
            'is_platform_admin' => true,
        ])->save();
    }
}
