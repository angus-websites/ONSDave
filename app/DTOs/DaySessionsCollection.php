<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JsonSerializable;

abstract class DaySessionsCollection implements JsonSerializable
{
    /**
     * @param  Carbon  $date [Date]
     * @param  Collection<DaySessions>  $days [Session]
     */
    public function __construct(
        public Carbon $date,
        public Collection $days,
    ) {
    }


    public abstract function toArray(): array;


    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * Get a DaySessions object for a given date
     *
     */
    public function getDay(Carbon $date): ?DaySessions
    {
        return $this->days->first(fn (DaySessions $day) => $day->date->isSameDay($date));
    }

    /**
     * Create a MonthSessions object from an array
     * @param array $data
     * @return MonthSessions
     */
    public static function fromArray(array $data): MonthSessions
    {
        return new MonthSessions(
            $data['date'],
            $data['days'],
        );
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
