<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JsonSerializable;

class MonthSessions extends DaySessionsCollection
{
    /**
     * @param  Carbon  $month [Month]
     * @param  Collection  $days [DaySessions]
     */
    public function __construct(
        public Carbon $month,
        public Collection $days,
    ) {
        parent::__construct($month, $days);
    }


    public function toArray(): array
    {
        return [
            'month' => $this->month->format('Y-m'),
            'days' => $this->days,
        ];
    }

}
