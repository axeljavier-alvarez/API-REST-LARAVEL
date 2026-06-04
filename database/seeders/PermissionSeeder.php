<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
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
        ];

        foreach($permissions as $permission){
            Permission::create([
                'name' => $permission,
                'guard_name' => 'api',
            ]); 
        }
    }
}
