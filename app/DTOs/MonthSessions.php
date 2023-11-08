<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class MonthSessions
{
    /**
     * @param Carbon $month [Month]
     * @param Collection $days [DaySessions]
     */
    public function __construct(
        public Carbon $month,
        public Collection $days,
    ){}

    public static function fromArray(array $data): MonthSessions
    {
        return new MonthSessions(
            $data['date'],
            $data['days'],
        );
    }
}

