<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\TimeRecordByDayResource;
use App\Models\Employee;
use App\Models\TimeRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TimeRecordByDayResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the resource with four records
     * which should result in two sessions
     */
    public function test_resource_organises_four_records_with_auto_clock_out(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecord::CLOCK_IN,
                'recorded_at' => ('2023-04-15 09:00:00'),
            ],
            (object) [
                'type' => TimeRecord::CLOCK_OUT,
                'recorded_at' => ('2023-04-15 13:00:00'),
            ],
            (object) [
                'type' => TimeRecord::CLOCK_IN,
                'recorded_at' => ('2023-04-15 14:00:00'),
            ],
            (object) [
                'type' => TimeRecord::AUTO_CLOCK_OUT,
                'recorded_at' => ('2023-04-15 18:00:00'),
            ],
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Expected result
        $expectedResult = [
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
        ];

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedResult, $resourceResult);
    }

    /**
     * Test the resource with a single record
     * which should result in a single session that is ongoing
     */
    public function test_resource_organises_records_with_ongoing_session(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecord::CLOCK_IN,
                'recorded_at' => ('2023-04-15 09:00:00'),
            ],
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Expected result
        $expectedResult = [
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
        ];

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedResult, $resourceResult);

    }

    /**
     * Test the resource some missing data
     * the clock_out records are missing
     */
    public function test_resource_with_some_missing_data(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecord::CLOCK_IN,
                'recorded_at' => ('2023-04-15 09:00:00'),
            ],
            (object) [
                'type' => TimeRecord::CLOCK_IN,
                'recorded_at' => ('2023-04-15 14:00:00'),
            ],
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Expected result
        $expectedResult = [
            'date' => '2023-04-15',
            'sessions' => [
                [
                    'clock_in' => '2023-04-15 09:00:00',
                    'clock_out' => null,
                    'duration' => null,
                    'ongoing' => true,
                    'auto_clock_out' => false,
                ],
                [
                    'clock_in' => '2023-04-15 14:00:00',
                    'clock_out' => null,
                    'duration' => null,
                    'ongoing' => true,
                    'auto_clock_out' => false,
                ],
            ],
        ];

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedResult, $resourceResult);
    }

    /**
     * Test the resource with no data
     */
    public function test_resource_with_no_data(): void
    {
        // Mockup some fake records
        $records = new Collection([]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Expected result
        $expectedResult = [
            'date' => '2023-04-15',
            'sessions' => [],
        ];

        // Assert that the entire structure matches our expectation
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
            'type' => TimeRecord::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecord::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecord::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecord::AUTO_CLOCK_OUT,
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
