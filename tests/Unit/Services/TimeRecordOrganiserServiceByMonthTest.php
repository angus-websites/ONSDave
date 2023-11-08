<?php

namespace Tests\Unit\Services;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Http\Resources\TimeRecordByMonthResource;
use DateInterval;
use PHPUnit\Framework\TestCase;
use App\Services\TimeRecordOrganiserService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimeRecordOrganiserServiceByMonthTest extends TestCase
{

    /**
     * Test the service with multiple records on different days
     * in the same month
     */
    public function test_organise_by_month(): void
    {
        // Generate some fake records
        $records = new Collection([
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
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-15 17:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-16 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-16 17:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-17 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-17 13:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-17 14:00:00'),
            ],
            (object) [
                'type' =>  TimeRecordType::AUTO_CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-17 15:00:00'),
            ],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Month to test
        $month = Carbon::parse('2023-04');

        // Call the service
        $monthSessions = $service->organiseRecordsByMonth($records, $month);

        // Assert the month is correct
        $this->assertEquals('2023-04', $monthSessions->getMonth()->format('Y-m'));


        // Assert the sessions are correct for the 15th
        $dayFifteen = $monthSessions->getDay(Carbon::parse('2023-04-15'));
        $dayFifteenSessions = $dayFifteen->getSessions();
        $this->assertEquals('2023-04-15 09:00:00', $dayFifteenSessions[0]->getClockIn()->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-04-15 13:00:00', $dayFifteenSessions[0]->getClockOut()->format('Y-m-d H:i:s'));
        $this->assertEquals('04:00:00', $dayFifteenSessions[0]->getDurationString());
        $this->assertFalse($dayFifteenSessions[0]->isOngoing());
        $this->assertFalse($dayFifteenSessions[0]->isAutoClockOut());
        $this->assertEquals('2023-04-15 14:00:00', $dayFifteenSessions[1]->getClockIn()->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-04-15 17:00:00', $dayFifteenSessions[1]->getClockOut()->format('Y-m-d H:i:s'));
        $this->assertEquals('03:00:00', $dayFifteenSessions[1]->getDurationString());
        $this->assertFalse($dayFifteenSessions[1]->isOngoing());
        $this->assertFalse($dayFifteenSessions[1]->isAutoClockOut());

        // Assert the sessions are correct for the 16th
        $daySixteen = $monthSessions->getDay(Carbon::parse('2023-04-16'));
        $daySixteenSessions = $daySixteen->getSessions();
        $this->assertEquals('2023-04-16 09:00:00', $daySixteenSessions[0]->getClockIn()->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-04-16 17:00:00', $daySixteenSessions[0]->getClockOut()->format('Y-m-d H:i:s'));
        $this->assertEquals('08:00:00', $daySixteenSessions[0]->getDurationString());
        $this->assertFalse($daySixteenSessions[0]->isOngoing());
        $this->assertFalse($daySixteenSessions[0]->isAutoClockOut());

        // Assert the sessions are correct for the 17th
        $daySeventeen = $monthSessions->getDay(Carbon::parse('2023-04-17'));
        $daySeventeenSessions = $daySeventeen->getSessions();
        $this->assertEquals('2023-04-17 09:00:00', $daySeventeenSessions[0]->getClockIn()->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-04-17 13:00:00', $daySeventeenSessions[0]->getClockOut()->format('Y-m-d H:i:s'));
        $this->assertEquals('04:00:00', $daySeventeenSessions[0]->getDurationString());
        $this->assertFalse($daySeventeenSessions[0]->isOngoing());
        $this->assertFalse($daySeventeenSessions[0]->isAutoClockOut());
        $this->assertEquals('2023-04-17 14:00:00', $daySeventeenSessions[1]->getClockIn()->format('Y-m-d H:i:s'));
        $this->assertEquals('2023-04-17 15:00:00', $daySeventeenSessions[1]->getClockOut()->format('Y-m-d H:i:s'));
        $this->assertEquals('01:00:00', $daySeventeenSessions[1]->getDurationString());
        $this->assertFalse($daySeventeenSessions[1]->isOngoing());
        $this->assertTrue($daySeventeenSessions[1]->isAutoClockOut());


    }
}
