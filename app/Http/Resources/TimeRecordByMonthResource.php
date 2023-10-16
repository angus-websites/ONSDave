<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;


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
        $recordsByDay = [];

        // Loop through each day in the month
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDay = $this->month->copy()->day($i);

            // Filter the records for the current day
            $dayRecords = $this->resource->filter(function ($record) use ($currentDay) {
                return $currentDay->isSameDay($record->recorded_at);
            })->values(); // We use values() to reset the keys to avoid index errors

            // Create a new TimeRecordByDayResource and resolve it to an array
            $recordsByDay[] = (new TimeRecordByDayResource($dayRecords, $currentDay))->resolve();
        }

        return [
            'month' => $this->month->format('Y-m'),
            'days' => $recordsByDay,
        ];
    }
}
