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

    public function test_resource_correctly_organizes_clock_in_and_clock_out_entries()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => Carbon::parse('2023-04-15 09:00:00')
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => Carbon::parse('2023-04-15 13:00:00')
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => Carbon::parse('2023-04-15 14:00:00')
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'auto_clock_out',
            'recorded_at' => Carbon::parse('2023-04-15 18:00:00')
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('history.fetch', ['date' => '2023-04-15']));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'clock_in' => '2023-04-15 09:00:00',
                        'clock_out' => '2023-04-15 13:00:00',
                        'duration' => '04:00:00',
                        'ongoing' => false,
                        'auto_clock_out' => false,
                    ],
                    [
                        'clock_in' => '2023-04-15 14:00:00',
                        'clock_out' => '2023-04-15 18:00:00',
                        'duration' => '04:00:00',
                        'ongoing' => false,
                        'auto_clock_out' => true,
                    ],
                ]
            ]);

    }



}
