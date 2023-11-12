<?php

namespace Tests\Unit\Services;

use App\DTOs\DaySessions;
use App\DTOs\Session;
use App\Services\TimeRecordStatService;
use Carbon\Carbon;
use DateInterval;
use PHPUnit\Framework\TestCase;

class TimeRecordStatServiceTest extends TestCase
{
    /**
     * Test we can calculate the total hours worked for the day
     */
    public function test_total_worked_hours(): void
    {
        $date = Carbon::parse('2023-04-15');

        // Create some time records
        $exampleSessions = collect([
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

        // Create an example DaySessions object
        $exampleDaySessions = new DaySessions(
            date: $date,
            sessions: $exampleSessions,
        );

        // Create a new instance of the service
        $statService = new TimeRecordStatService();

        // Pass the example DaySessions object to the method we want to test
        $totalTimeWorked = $statService->calculateTotalTimeWorkedForDay($exampleDaySessions);

        // Assert the total hours worked is 8
        $this->assertEquals(8, $totalTimeWorked['hours']);
    }

    /**
     * Test we can calculate the total hours, minutes and seconds worked for the day
     */
    public function test_total_worked_hours_minutes_seconds(): void
    {
        $date = Carbon::parse('2023-04-15');

        // Create some time records
        $exampleSessions = collect([
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 09:02:55'),
                'clock_out' => Carbon::parse('2023-04-15 13:17:09'),
                'ongoing' => false,
                'auto_clock_out' => false,
            ]),
            Session::fromArray([
                'clock_in' => Carbon::parse('2023-04-15 14:30:00'),
                'clock_out' => Carbon::parse('2023-04-15 19:09:52'),
                'ongoing' => false,
                'auto_clock_out' => true,
            ]),
        ]);

        // Create an example DaySessions object
        $exampleDaySessions = new DaySessions(
            date: $date,
            sessions: $exampleSessions,
        );

        // Create a new instance of the service
        $statService = new TimeRecordStatService();

        // Pass the example DaySessions object to the method we want to test
        $totalTimeWorked = $statService->calculateTotalTimeWorkedForDay($exampleDaySessions);

        //Assert the worked hours, minutes, and seconds are correct
        $this->assertEquals(8, $totalTimeWorked['hours']);
        $this->assertEquals(54, $totalTimeWorked['minutes']);
        $this->assertEquals(6, $totalTimeWorked['seconds']);

    }
}
