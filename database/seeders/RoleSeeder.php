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
        Permission::create(['name' => 'can specify clock time']);
        Permission::create(['name' => 'can manage other employees']);


        // Creating roles for users
        Role::create(['name' => 'super admin']);
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);

        // Creating roles for employees
        Role::create(['name' => 'employee manager'])
            ->syncPermissions(['can manage other employees', 'can specify clock time']);
        Role::create(['name' => 'employee standard'])
            ->syncPermissions(['can specify clock time']);;
        Role::create(['name' => 'employee restricted']);



    }
}
