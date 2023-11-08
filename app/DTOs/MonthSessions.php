<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JsonSerializable;

class MonthSessions implements JsonSerializable
{
    /**
     * @param  Carbon  $month [Month]
     * @param  Collection  $days [DaySessions]
     */
    public function __construct(
        public Carbon $month,
        public Collection $days,
    ) {
    }

    /**
     * Get the month for this object
     */
    public function getMonth(): Carbon
    {
        return $this->month;
    }

    /**
     * Get a DaySessions object for a given date
     */
    public function getDay(Carbon $date): ?DaySessions
    {
        return $this->days->first(fn (DaySessions $day) => $day->date->isSameDay($date));
    }

    /**
     * Create a MonthSessions object from an array
     */
    public static function fromArray(array $data): MonthSessions
    {
        return new MonthSessions(
            $data['date'],
            $data['days'],
        );
    }

    public function toArray(): array
    {
        return [
            'month' => $this->month->format('Y-m'),
            'days' => $this->days,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
