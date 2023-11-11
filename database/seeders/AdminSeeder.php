<?php

namespace Database\Seeders;

use App\Models\Employee;
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

        if (config('admin.admin_name')) {
            $admin = User::create([
                'name' => config('admin.admin_name'),
                'email' => config('admin.admin_email'),
                'password' => Hash::make(config('admin.admin_password')),
            ]);

            // Assign the super admin role
            $admin->assignRole('super admin');

            $admin_employee = Employee::create([
                'user_id' => $admin->id,
            ]);

            // Assign the manager role
            $admin_employee->assignRole('employee manager');

        }

    }
}
