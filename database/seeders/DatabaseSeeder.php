<?php

namespace Database\Seeders;

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
        // Create the admin
        $this->call(AdminSeeder::class);

        // Create 10 users and assign them a standard user and employee role
        \App\Models\User::factory(10)->create()->each(function ($user) {
            $user->assignRole(['employee standard', 'user']);
        });

    }
}
