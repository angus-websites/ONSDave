<?php

namespace App\DTOs;

use Carbon\Carbon;
use DateInterval;
use JsonSerializable;

class Session implements JsonSerializable
{
    private ?DateInterval $duration = null;

    private bool $multiDay = false;

    public function __construct(
        public Carbon $clockIn,
        public ?Carbon $clockOut,
        public bool $ongoing,
        public bool $autoClockOut,
    ) {
        // Calculate the duration if the clock out time is not null
        if ($this->clockOut !== null) {
            $this->duration = $this->clockIn->diff($this->clockOut);
        }

        // Check if the session is multi-day
        if ($this->clockOut && $this->clockOut->format('Y-m-d') > $this->clockIn->format('Y-m-d')) {
            $this->multiDay = true;
        }
    }

    public function getClockIn(): Carbon
    {
        return $this->clockIn;
    }

    public function getClockOut(): ?Carbon
    {
        return $this->clockOut;
    }

    public function getDurationInSeconds(): int
    {
        // Check if the DateInterval property is not null
        if ($this->duration !== null) {
            // Calculate total seconds
            return ($this->duration->h * 3600) +
                ($this->duration->i * 60) +
                $this->duration->s;
        }

        // If the DateInterval is null, return 0
        return 0;
    }

    public function getDurationString(): ?string
    {
        return $this->duration?->format('%H:%I:%S');
    }

    public function isOngoing(): bool
    {
        return $this->ongoing;
    }

    public function isMultiDay(): bool
    {
        return $this->multiDay;
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
            $data['ongoing'],
            $data['auto_clock_out'],
        );
    }

    public function toArray(): array
    {
        return [
            'clock_in' => $this->getClockIn()->format('Y-m-d H:i:s'),
            'clock_out' => $this->getClockOut()?->format('Y-m-d H:i:s'),
            'duration' => $this->getDurationString(),
            'duration_in_seconds' => $this->getDurationInSeconds(),
            'ongoing' => $this->isOngoing(),
            'auto_clock_out' => $this->isAutoClockOut(),
            'multi_day' => $this->isMultiDay(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
