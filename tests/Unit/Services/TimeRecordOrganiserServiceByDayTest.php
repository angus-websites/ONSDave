<?php

namespace Tests\Unit\Services;

use App\DTOs\DaySessions;
use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Services\TimeRecordOrganiserService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class TimeRecordOrganiserServiceByDayTest extends TestCase
{
    /**
     * Test the service by day with four records
     * which should result in two sessions
     */
    public function test_organise_records_by_day_into_sessions()
    {
        // Generate some fake records
        $records = collect([
            (object) ['type' => TimeRecordType::CLOCK_IN, 'recorded_at' => new Carbon('2023-04-15 09:00:00')],
            (object) ['type' => TimeRecordType::CLOCK_OUT, 'recorded_at' => new Carbon('2023-04-15 13:00:00')],
            (object) ['type' => TimeRecordType::CLOCK_IN, 'recorded_at' => new Carbon('2023-04-15 14:00:00')],
            (object) ['type' => TimeRecordType::AUTO_CLOCK_OUT, 'recorded_at' => new Carbon('2023-04-15 18:00:00')],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Set date
        $date = Carbon::parse('2023-04-15');

        // Call the method we want to test
        $actualDaySessions = $service->organiseRecordsByDay($records, $date);

        // Assert
        $expectedSessions = collect([
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 09:00:00'),
                'clock_out' => Carbon::parse('2023-04-15 13:00:00'),
                'ongoing' => false,
                'auto_clock_out' => false,
            ]),
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 14:00:00'),
                'clock_out' => Carbon::parse('2023-04-15 18:00:00'),
                'ongoing' => false,
                'auto_clock_out' => true,
            ]),
        ]);

        $expectedDaySessions = new DaySessions(
            date: $date,
            sessions: $expectedSessions,
        );

        $this->assertEquals($expectedDaySessions, $actualDaySessions);
    }

    /**
     * Test the service with a single record
     * which should result in a single session that is ongoing
     */
    public function test_organise_records_by_day_with_ongoing_session(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
            ],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Set date
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the service
        $actualDaySessions = $service->organiseRecordsByDay($records, $date);

        // Expected result a collection with a single session
        $expectedSessions = collect([
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 09:00:00'),
                'clock_out' => null,
                'ongoing' => true,
                'auto_clock_out' => false,
            ]),
        ]);

        // Expected result
        $expectedDaySessions = new DaySessions(
            date: $date,
            sessions: $expectedSessions,
        );

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedDaySessions, $actualDaySessions);

    }

    /**
     * Test the service with some missing data
     * the clock_out records are missing
     */
    public function test_organise_records_by_day_with_some_missing_data(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
            ],
        ]);

        // Set date
        $date = Carbon::parse('2023-04-15');

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Pass the records to the service
        $actualDaySessions = $service->organiseRecordsByDay($records, $date);

        // Create a collection with the expected sessions
        $expectedSessions = collect([
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 09:00:00'),
                'clock_out' => null,
                'ongoing' => true,
                'auto_clock_out' => false,
            ]),
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 14:00:00'),
                'clock_out' => null,
                'ongoing' => true,
                'auto_clock_out' => false,
            ]),
        ]);

        // Expected result
        $expectedDaySessions = new DaySessions(
            date: $date,
            sessions: $expectedSessions,
        );

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedDaySessions, $actualDaySessions);
    }

    /**
     * Test the service with no data
     */
    public function test_organise_resource_by_day_with_no_data(): void
    {
        // Mockup some fake records
        $records = new Collection([]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Pass the records to the service
        $actualDaySessions = $service->organiseRecordsByDay($records, $date);

        // Expected result
        $expectedSessions = collect([]);

        // Expected result
        $expectedDaySessions = new DaySessions(
            date: $date,
            sessions: $expectedSessions,
        );

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedDaySessions, $actualDaySessions);
    }
}
