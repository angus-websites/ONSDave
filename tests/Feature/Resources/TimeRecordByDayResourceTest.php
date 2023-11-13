<?php

namespace Tests\Feature\Resources;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimeRecordByDayResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the resource at a http endpoint
     */
    public function test_resource_fetch_by_date_endpoint_works()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

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
        $employee = Employee::factory()->create();
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
}
