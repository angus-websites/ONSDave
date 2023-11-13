<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Spatie\Permission\Models\Role;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
        ];
    }

    /**
     * Assign a role to the employee after creation.
     *
     * @param  string  $roleName
     */
    public function withRole($roleName)
    {
        return $this->afterCreating(function (Employee $employee) use ($roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $employee->assignRole($role);
        });
    }
}
