<?php

namespace Database\Seeders;

use App\Models\EmployeeRole;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Create the roles
     * for the users
     * in the application
     *
     * @return void
     */
    public function run()
    {

        Schema::disableForeignKeyConstraints();
        //Clear data
        User::query()->delete();
        Role::query()->delete();
        EmployeeRole::query()->delete();
        Schema::enableForeignKeyConstraints();

        // User
        Role::create([
            'id' => 1,
            'name' => 'User',
            'code' => 'Usr',
            'description' => 'A normal user of the application',
        ]);

        // Admin
        Role::create([
            'id' => 2,
            'name' => 'Admin',
            'code' => 'Am',
            'description' => 'As an admin you have full control of the application',
        ]);

        // Super Admin
        Role::create([
            'id' => 3,
            'name' => 'Super Admin',
            'code' => 'Sam',
            'description' => 'The Goat',
            'changeable' => 0,
        ]);

        // ========= Employee Roles =========

        EmployeeRole::create([
            'id' => 1,
            'name' => 'Standard User',
            'code' => 'Usr',
            'description' => 'A standard user that has the ability to manually enter their start and end times',
        ]);

        EmployeeRole::create([
            'id' => 2,
            'name' => 'Restricted User',
            'code' => 'RUsr',
            'description' => 'A user that does not have the ability to manually enter their start and end times',
        ]);

    }
}
