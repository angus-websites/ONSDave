<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed the roles
        $this->call(RoleSeeder::class);
        // Seed the leave types
        $this->call(LeaveTypeSeeder::class);
        // Create the admin
        $this->call(AdminSeeder::class);

        // Create 10 employees and assign roles to each (dev purposes)
        Employee::factory(10)->create()->each(function (Employee $employee) {
            $employee->assignRole('employee');
            $employee->user->assignRole('user');
        });

    }
}
