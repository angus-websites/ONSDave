<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class TimeRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'data' => $this->organizeRecords($this->resource),
        ];
    }

    /**
     * Organize the records into paired clock in and clock out entries.
     *
     * @param  \Illuminate\Support\Collection  $records
     * @return array
     */
    protected function organizeRecords($records): array
    {
        $organized = [];
        $temp = null;

        foreach ($records as $record) {
            if ($this->isClockIn($record)) {
                $temp = $this->startClockInSession($record);
            } elseif ($this->isClockOut($record) && $temp) {
                $temp = $this->completeClockOutSession($temp, $record);
                $organized[] = $temp;
                $temp = null;
            }
        }

        // Handle cases where there's a clock-in without a corresponding clock-out
        if ($temp) {
            $organized[] = $this->markSessionAsOngoing($temp);
        }

        return $organized;
    }

    protected function isClockIn($record): bool
    {
        return $record->type === 'clock_in';
    }

    protected function isClockOut($record): bool
    {
        return $record->type === 'clock_out' || $record->type === 'auto_clock_out';
    }

    protected function startClockInSession($record): array
    {
        return ['clock_in' => $record->recorded_at, 'ongoing' => true];
    }

    protected function completeClockOutSession(array $session, $record): array
    {
        $session['clock_out'] = $record->recorded_at;
        $session['duration'] = $this->calculateDuration($session['clock_in'], $session['clock_out']);
        $session['ongoing'] = false;

        return $session;
    }

    protected function markSessionAsOngoing(array $session): array
    {
        $session['clock_out'] = null;
        $session['duration'] = 'N/A';

        return $session;
    }

    /**
     * Calculate the duration between start and end times.
     *
     * @param  string  $start
     * @param  string  $end
     * @return string
     */
    protected function calculateDuration(string $start, string $end): string
    {
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);

        return $startTime->diff($endTime)->format('%H:%I:%S');
    }
}
