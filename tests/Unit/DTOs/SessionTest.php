<?php

namespace Tests\Unit;

use App\DTOs\Session;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * Test a session returns correct duration in seconds.
     */
    public function test_session_returns_correct_duration_in_seconds_for_one_hour(): void
    {

        $clockIn = Carbon::parse('2023-04-15 09:00:00');
        $clockOut = Carbon::parse('2023-04-15 10:00:00');

        $session = new Session(
            clockIn: $clockIn,
            clockOut: $clockOut,
            ongoing: false,
            autoClockOut: false,
        );

        $this->assertEquals(3600, $session->getDurationInSeconds());
    }

    /**
     * Test a session returns correct duration in seconds for more complex duration.
     */
    public function test_session_returns_correct_duration_in_seconds_for_more_complex_duration(): void
    {
        $clockIn = Carbon::parse('2023-04-15 14:30:00');
        $clockOut = Carbon::parse('2023-04-15 19:09:52');

        $session = new Session(
            clockIn: $clockIn,
            clockOut: $clockOut,
            ongoing: false,
            autoClockOut: false,
        );

        $this->assertEquals(16792, $session->getDurationInSeconds());
    }

}
