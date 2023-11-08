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

    public function getClockIn(): Carbon
    {
        return $this->clockIn;
    }

    public function getClockOut(): ?Carbon
    {
        return $this->clockOut;
    }

    public function getDuration(): ?DateInterval
    {
        return $this->duration;
    }

    public function getDurationString(): ?string
    {
        return $this->duration ? $this->duration->format('%H:%I:%S') : null;
    }

    public function isOngoing(): bool
    {
        return $this->ongoing;
    }

    public function isAutoClockOut(): bool
    {
        return $this->autoClockOut;
    }


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
