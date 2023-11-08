<?php

namespace App\DTOs;

use Carbon\Carbon;
use DateInterval;
use JsonSerializable;

class Session implements JsonSerializable
{
    public function __construct(
        public Carbon $clockIn,
        public ?Carbon $clockOut,
        public ?DateInterval $duration,
        public bool $ongoing,
        public bool $autoClockOut,
    ) {
    }

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
        return $this->duration?->format('%H:%I:%S');
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

    public function toArray(): array
    {
        return [
            'clock_in' => $this->getClockIn()->format('Y-m-d H:i:s'),
            'clock_out' => $this->getClockOut()->format('Y-m-d H:i:s'),
            'duration' => $this->getDurationString(),
            'ongoing' => $this->isOngoing(),
            'auto_clock_out' => $this->isAutoClockOut(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
