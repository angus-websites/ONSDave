<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class TimeRecordByMonthResource extends ResourceCollection
{
    protected $month;

    public function __construct($resource, Carbon $month)
    {
        parent::__construct($resource);
        $this->month = $month;
    }

    public function toArray($request)
    {
        $daysInMonth = $this->month->daysInMonth;
        $recordsByDay = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDay = $this->month->copy()->day($i);
            $dayRecords = $this->resource->filter(function ($record) use ($currentDay) {
                return $record->recorded_at->isSameDay($currentDay);
            });

            $recordsByDay[] = (new TimeRecordByDayResource($dayRecords, $currentDay))->resolve();
        }

        return [
            'month' => $this->month->format('Y-m'),
            'days' => $recordsByDay,
        ];
    }
}
