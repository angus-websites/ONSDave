<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    /**
     * Seed the different leave types
     */
    public function run(): void
    {
        $leave_types = [
            ['name' => 'Annual leave', 'code' => 'annual', 'description' => 'Annual leave taken from the employees balance', 'has_balance' => true],
            ['name' => 'Privilege leave', 'code' => 'privilege', 'description' => 'Privilege leave used for annual privilege days', 'has_balance' => true],
            ['name' => 'Flexi leave', 'code' => 'flexi', 'description' => 'Using accumulated Flexi as leave', 'has_balance' => false],
            ['name' => 'Sickness', 'code' => 'sick', 'description' => 'Cannot attend work due to illness / sickness', 'has_balance' => false],
            ['name' => 'Other', 'code' => 'other', 'description' => 'Any other reason for leave', 'has_balance' => false],
        ];

        // Create the leave types
        foreach ($leave_types as $leave_type) {
            LeaveType::create($leave_type);
        }
    }
}
