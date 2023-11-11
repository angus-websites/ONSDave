<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $superAdminRole = Role::where('name', '=', 'Super Admin')->firstOrFail();

        if (config('admin.admin_name')) {
            $admin = User::create([
                'name' => config('admin.admin_name'),
                'email' => config('admin.admin_email'),
                'password' => Hash::make(config('admin.admin_password')),
                'role_id' => $superAdminRole->id,
            ]);

            Employee::create([
                'user_id' => $admin->id,
            ]);
        }

    }
}
