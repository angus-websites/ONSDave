<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Models\TimeRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class TimeRecordTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_can_clock_in()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Simulate a POST request to the time-records.store route
        $this->post(route('time-records.store'));

        // Assert the record was created in the database
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => TimeRecord::CLOCK_IN,
            'recorded_at' => now()->toDateTimeString()
        ]);
    }

    public function test_employee_can_clock_out_after_clocking_in()
    {
        $user = User::factory()->create();
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user);

        // Simulate a POST request to the time-records.store route
        $this->post(route('time-records.store'));

        // Assert the record was created in the database
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => TimeRecord::CLOCK_IN,
            'recorded_at' => now()->toDateTimeString()
        ]);

        // Simulate a POST request to the time-records.store route
        $this->post(route('time-records.store'));

        // Assert the record was created in the database
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => TimeRecord::CLOCK_OUT,
            'recorded_at' => now()->toDateTimeString()
        ]);
    }

}
