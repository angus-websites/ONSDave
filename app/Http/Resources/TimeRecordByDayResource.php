<?php

namespace App\Http\Resources;

use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;

class TimeRecordByDayResource extends ResourceCollection
{
    protected Carbon $date;

    public function __construct(Collection $resource, Carbon $date)
    {
        parent::__construct($resource);
        $this->date = $date;
    }

    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'sessions' => $this->organizeRecords($this->collection),
        ];
    }

    /**
     * @return array
     * This method organizes the records into sessions, a session is a clock in and clock out
     * along with the duration of the session.
     */
    private function organizeRecords(Collection $records): array
    {
        $organized = [];
        $count = count($records);

        for ($i = 0; $i < $count; $i++) {
            $record = $records[$i];

            // If the record is a clock in then we need to find the next clock out
            if ($record->type === TimeRecord::CLOCK_IN) {
                $nextRecord = ($i + 1) < $count ? $records[$i + 1] : null;

                $ongoing = ! $nextRecord || ! in_array($nextRecord->type, [TimeRecord::CLOCK_OUT, TimeRecord::AUTO_CLOCK_OUT]);
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

    /**
     * @return string|null
     * This method calculates the duration between two dates
     */
    private function calculateDuration(?Carbon $start, ?Carbon $end): ?string
    {
        if (! $start || ! $end) {
            return null;
        }

        return $start->diff($end)->format('%H:%I:%S');
    }
}
