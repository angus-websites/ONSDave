<?php

namespace Tests\Feature\Resources;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimeRecordByDayResourceTest extends TestCase
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
    public function test_resource_fetch_by_date_endpoint_works()
    {
        $employee = $this->standard_employee;
        $this->actingAs($this->standard_employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::AUTO_CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 18:00:00'),
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('api.sessions.day', ['date' => '2023-04-15']));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => '2023-04-15',
                    'sessions' => [
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
                    ],
                ],
            ]);

    }

    /**
     * Test the resource at a http endpoint with an ongoing session
     */
    public function test_resource_fetch_by_date_endpoint_works_with_ongoing_session()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('api.sessions.day', ['date' => '2023-04-15']));

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => '2023-04-15',
                    'sessions' => [
                        [
                            'clock_in' => '2023-04-15 09:00:00',
                            'clock_out' => null,
                            'duration' => null,
                            'ongoing' => true,
                            'auto_clock_out' => false,
                        ],
                    ],
                ],
            ]);

    }

    public function test_resource_fetch_by_date_for_multi_day_session()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-14 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-16 09:00:00'),
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('api.sessions.day', ['date' => '2023-04-14']));

        // Assert that the response is correct
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'date' => '2023-04-15',
                    'sessions' => [
                        [
                            'clock_in' => '2023-04-14 09:00:00',
                            'clock_out' => '2023-04-16 09:00:00',
                            'duration' => '48:00:00',
                            'ongoing' => false,
                            'auto_clock_out' => false,
                            'multi_day' => true,
                        ],
                    ],
                ],
            ]);

    }
}
