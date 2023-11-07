<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TimeRecordByMonthResource extends ResourceCollection
{
    protected Carbon $month;

    public function __construct(Collection $resource, Carbon $month)
    {
        parent::__construct($resource);
        $this->month = $month;
    }

    public function toArray(Request $request): array
    {
        $daysInMonth = $this->month->daysInMonth;
        $sessionsByDay = [];

        // Loop through each day in the month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDay = $this->month->copy()->day($i);

            // Filter the sessions for the current day
            $daySessions = $this->resource->filter(function ($record) use ($currentDay) {
                return $currentDay->isSameDay($record->recorded_at);
            })->values(); // We use values() to reset the keys to avoid index errors

            // Create a new TimeRecordByDayResource and resolve it to an array
            $sessionsByDay[] = (new TimeRecordByDayResource($daySessions, $currentDay))->resolve();
        }

        return [
            'month' => $this->month->format('Y-m'),
            'days' => $sessionsByDay,
        ];
    }
}
