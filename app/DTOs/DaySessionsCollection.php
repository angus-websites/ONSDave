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

    abstract public function toArray(): array;

    abstract public static function fromArray(array $data): DaySessionsCollection;

    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * Get a DaySessionsCollection object for a given date
     */
    public function getDay(Carbon $date): ?DaySessions
    {
        return $this->days->first(fn (DaySessions $day) => $day->date->isSameDay($date));
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
