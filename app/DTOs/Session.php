<?php

namespace App\DTOs;

use Carbon\Carbon;
use DateInterval;

class Session
{
    /**
     * @param Carbon $clockIn
     * @param Carbon|null $clockOut
     * @param DateInterval|null $duration
     * @param bool $ongoing
     * @param bool $autoClockOut
     */
    public function __construct(
        public Carbon $clockIn,
        public ?Carbon $clockOut,
        public ?DateInterval $duration,
        public bool $ongoing,
        public bool $autoClockOut,
    ){}


    public static function fromArray(array $data): Session
    {
        return new Session(
            $data['clock_in'],
            $data['clock_out'],
            $data['duration'],
            $data['ongoing'],
            $data['auto_clock_out'],
        );
    }
}
