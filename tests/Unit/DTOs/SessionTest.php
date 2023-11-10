<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    /**
     * Test a session returns correct duration in seconds.
     */
    public function test_session_returns_correct_duration_in_seconds_for_one_hour(): void
    {

        $now = Carbon::parse('2023-04-15 09:00:00');

        $session = new \App\DTOs\Session(
            clockIn: $now,
            clockOut: $now->addHours(1),
            duration: new \DateInterval('PT1H'),
            ongoing: false,
            autoClockOut: false,
        );

        $this->assertEquals(3600, $session->getDurationInSeconds());
    }

}
