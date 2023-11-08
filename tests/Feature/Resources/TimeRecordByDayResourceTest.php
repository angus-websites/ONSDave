<?php

namespace Tests\Feature\Resources;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Http\Resources\TimeRecordByDayResource;
use App\Models\Employee;
use App\Models\TimeRecord;
use DateInterval;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TimeRecordByDayResourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_resource_returns_correct_data()
    {
        // Create a collection of TimeRecord objects
        $timeRecords = new Collection([
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::AUTO_CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-15 18:00:00'),
            ],
        ]);

        $date = Carbon::parse('2023-04-15');

        // Create a new TimeRecordByDayResource instance
        $resourceResult = (new TimeRecordByDayResource($timeRecords, $date))->toArray(request());

        // Create the expected result
        $expectedResult = [
            'date' => '2023-04-15',
            'sessions' => collect([
                Session::fromArray([
                    'clock_in' => Carbon::parse('2023-04-15 09:00:00'),
                    'clock_out' => Carbon::parse('2023-04-15 13:00:00'),
                    'duration' => new DateInterval('PT4H'),
                    'ongoing' => false,
                    'auto_clock_out' => false,
                ]),
                Session::fromArray([
                    'clock_in' => Carbon::parse('2023-04-15 14:00:00'),
                    'clock_out' => Carbon::parse('2023-04-15 18:00:00'),
                    'duration' => new DateInterval('PT4H'),
                    'ongoing' => false,
                    'auto_clock_out' => true,
                ]),
            ]),
        ];

        // Assert that the resource result matches the expected result
        $this->assertEquals($expectedResult, $resourceResult);
    }

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
        $response = $this->post(route('history.day.fetch', ['date' => '2023-04-15']));

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
}
