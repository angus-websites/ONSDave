<?php

namespace Tests\Unit\Services;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Http\Resources\TimeRecordByDayResource;
use DateInterval;
use PHPUnit\Framework\TestCase;
use App\Services\TimeRecordOrganizerService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimeRecordOrganizerServiceTest extends TestCase
{
    public function test_organize_records_into_sessions()
    {
        // Generate some fake records
        $records = collect([
            (object) ['type' => TimeRecordType::CLOCK_IN, 'recorded_at' => new Carbon('2023-04-15 09:00:00')],
            (object) ['type' => TimeRecordType::CLOCK_OUT, 'recorded_at' => new Carbon('2023-04-15 13:00:00')],
            (object) ['type' => TimeRecordType::CLOCK_IN, 'recorded_at' => new Carbon('2023-04-15 14:00:00')],
            (object) ['type' => TimeRecordType::AUTO_CLOCK_OUT, 'recorded_at' => new Carbon('2023-04-15 18:00:00')],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganizerService();

        // Call the method we want to test
        $sessions = $service->organizeRecordsByDay($records);

        // Assert
        $expectedSessions = collect([
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
            ])
        ]);

        $this->assertEquals($expectedSessions, $sessions);
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
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
            ],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganizerService();

        // Pass the records to the service
        $sessions = $service->organizeRecordsByDay($records);

        // Expected result a collection with a single session
        $expectedResult = collect([
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 09:00:00'),
                'clock_out' => null,
                'duration' => null,
                'ongoing' => true,
                'auto_clock_out' => false,
            ])
        ]);

        // Assert that the entire structure matches our expectation
        $this->assertEquals($expectedResult, $sessions);

    }

}
