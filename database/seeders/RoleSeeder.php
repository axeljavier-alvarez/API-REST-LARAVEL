<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $role = Role::create([
            'name' => 'admin',
            'guard_name' => 'api',
        ]);

        $role->syncPermissions([
            'permissions.index',
            'permissions.store',
            'permissions.show',
            'permissions.update',
            'permissions.destroy',
            'roles.index',
            'roles.store',
            'roles.show',
            'roles.update',
            'roles.destroy',
        ]);
    }
}
