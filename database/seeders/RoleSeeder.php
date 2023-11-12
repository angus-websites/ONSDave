<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Create the roles
     * and permissions
     * @return void
     */
    public function run()
    {

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'time_records.*' => 'employee',
            'time_records.specify_clock_time' => 'employee',
            'time_records.create' => 'employee',
            'time_records.read' => 'employee',
            'time_records.update' => 'employee',
            'time_records.delete' => 'employee',
            'others.time_records.*' => 'employee',
            'others.time_records.create' => 'employee',
            'others.time_records.read' => 'employee',
            'others.time_records.update' => 'employee',
            'others.time_records.delete' => 'employee',
        ];

        // Define roles with their guard and permissions
        $roles = [
            'super admin' => [
                'guard' => 'web',
                'permissions' => [],
            ],
            'admin' => [
                'guard' => 'web',
                'permissions' => [],
            ],
            'user' => [
                'guard' => 'web',
                'permissions' => [],
            ],
            'employee' => [
                'guard' => 'employee',
                'permissions' => [
                    'time_records.*',
                ],
            ],
            'manager' => [
                'guard' => 'employee',
                'permissions' => [
                    'others.time_records.*',
                ],
            ],
            'employee restricted' => [
                'guard' => 'employee',
                'permissions' => [
                    'time_records.create',
                ],
            ],
        ];

        // Create the permissions
        foreach ($permissions as $permission => $guard) {
            Permission::create(['name' => $permission, 'guard_name' => $guard]);
        }
        // Create the roles
        foreach ($roles as $role => $data) {
            $role = Role::create(['name' => $role, 'guard_name' => $data['guard']]);
            // Assign the permissions to the role
            foreach ($data['permissions'] as $permission) {
                $role->givePermissionTo($permission);
            }
        }


    }
}
