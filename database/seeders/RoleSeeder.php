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

        // Creating permissions
        Permission::create(['name' => 'can specify clock time', 'guard_name' => 'employee']);
        Permission::create(['name' => 'can manage other employees', 'guard_name' => 'employee']);


        // Creating roles for users
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Creating roles for employees (NOTE, all employee roles and permissions need the employee guard)
        Role::create(['name' => 'employee manager', 'guard_name' => 'employee'])
            ->syncPermissions(['can manage other employees', 'can specify clock time']);
        Role::create(['name' => 'employee standard', 'guard_name' => 'employee'])
            ->syncPermissions(['can specify clock time']);;
        Role::create(['name' => 'employee restricted', 'guard_name' => 'employee' ]);



    }
}
