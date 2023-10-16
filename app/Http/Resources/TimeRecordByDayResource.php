<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimeRecordByDayResource extends ResourceCollection
{
    protected Carbon $date;

    public function __construct($resource, Carbon $date)
    {
        parent::__construct($resource);
        $this->date = $date;
    }

    public function toArray($request)
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'records' => $this->organizeRecords($this->collection),
        ];
    }

    private function organizeRecords($records)
    {
        $organized = [];
        $count = count($records);

        for ($i = 0; $i < $count; $i++) {
            $record = $records[$i];

            if ($record->type === 'clock_in') {
                $nextRecord = ($i + 1) < $count ? $records[$i + 1] : null;

                $ongoing = !$nextRecord || !in_array($nextRecord->type, ['clock_out', 'auto_clock_out']);
                $isAutoClockOut = $nextRecord && $nextRecord->type === 'auto_clock_out';

                $organized[] = [
                    'clock_in' => $record->recorded_at,
                    'clock_out' => $ongoing ? null : $nextRecord->recorded_at,
                    'duration' => $this->calculateDuration(Carbon::parse($record->recorded_at), $ongoing ? null : Carbon::parse($nextRecord->recorded_at)),
                    'ongoing' => $ongoing,
                    'auto_clock_out' => $isAutoClockOut,
                ];
            }
        }

        return $organized;
    }

    private function calculateDuration(?Carbon $start, ?Carbon $end): ?string
    {
        if (!$start || !$end) {
            return null;
        }

        return $start->diff($end)->format('%H:%I:%S');
    }
}

