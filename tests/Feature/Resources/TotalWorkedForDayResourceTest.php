<?php

namespace Feature\Resources;

use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TotalWorkedForDayResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->standard_employee = Employee::factory()->withRole('employee')->create();
        $this->restricted_employee = Employee::factory()->withRole('employee restricted')->create();

    }

    /**
     * Test the resource at a http endpoint
     */
    public function test_resource_formats_total_worked_today_correctly()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 09:15:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 14:00:22'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::AUTO_CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 18:00:00'),
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('api.sessions.total.day', ['date' => '2023-04-15']));

        $response->assertStatus(200)
            ->assertJson([
                'hours' => '07',
                'minutes' => '44',
                'seconds' => '38',
            ]);

    }

    /**
     * Test when a user clocks in and then out on different days, the total time worked is split correctly
     */
    public function test_resource_splits_total_worked_correctly_when_clock_in_and_out_on_different_days()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Clock in at 11pm
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 23:00:00'),
        ]);

        // Clock out at 2:15am the next day
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-16 02:15:00'),
        ]);

        // Check the first day
        $day1Response = $this->post(route('api.sessions.total.day', ['date' => '2023-04-15']));

        // If a clock in and out occur on different days, the day ends at midnight
        $day1Response->assertStatus(200)
            ->assertJson([
                'hours' => '01',
                'minutes' => '00',
                'seconds' => '00',
            ]);

        // Check the second day
        $day1Response = $this->post(route('api.sessions.total.day', ['date' => '2023-04-16']));

        $day1Response->assertStatus(200)
            ->assertJson([
                'hours' => '02',
                'minutes' => '15',
                'seconds' => '00',
            ]);
    }
}
