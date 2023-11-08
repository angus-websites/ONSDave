<?php

namespace App\Services;

use App\DTOs\DaySessions;
use App\DTOs\MonthSessions;
use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Http\Resources\TimeRecordByDayResource;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Support\Collection;


class TimeRecordOrganiserService
{
    /**
     * Organize the records into sessions with clock in, clock out, and duration.
     *
     * @param Collection $records
     * @return DaySessions
     */
    public function organiseRecordsByDay(Collection $records, Carbon $date): DaySessions
    {
        $organizedSessions = collect();
        $count = count($records);

        for ($i = 0; $i < $count; $i++) {
            $record = $records[$i];

            if ($record->type === TimeRecordType::CLOCK_IN) {
                $nextRecord = ($i + 1) < $count ? $records[$i + 1] : null;

                $ongoing = !$nextRecord || !in_array($nextRecord->type, [TimeRecordType::CLOCK_OUT, TimeRecordType::AUTO_CLOCK_OUT]);
                $isAutoClockOut = $nextRecord && $nextRecord->type === TimeRecordType::AUTO_CLOCK_OUT;
                $clockOutTime = $ongoing ? null : $nextRecord->recorded_at;

                // Create a new session object using named parameters
                $session = new Session(
                    clockIn: $record->recorded_at,
                    clockOut: $clockOutTime,
                    duration: $this->calculateDuration($record->recorded_at, $clockOutTime),
                    ongoing: $ongoing,
                    autoClockOut: $isAutoClockOut,
                );

                $organizedSessions->push($session);
            }
        }

        return new DaySessions(
            date: $date,
            sessions: $organizedSessions,
        );
    }

    /**
     * Organize time records by month.
     *
     * @param Collection $records
     * @param Carbon $month
     * @return MonthSessions
     */
    public function organiseRecordsByMonth(Collection $records, Carbon $month): MonthSessions
    {
        $daysInMonth = $month->daysInMonth;
        $monthSessions = collect();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDay = $month->copy()->day($i);

            // Filter the records to only include the current day
            $dayRecords = $records->filter(function ($record) use ($currentDay) {
                return $currentDay->isSameDay($record->recorded_at);
            })->values();

            $daySessions = new DaySessions(
                date: $currentDay,
                sessions: $this->organiseRecordsByDay($dayRecords, $currentDay),
            );

            // Add the day sessions to the collection
            $monthSessions->push($daySessions);

        }

        return new MonthSessions(
            month: $month,
            days: $monthSessions,
        );

    }


    /**
     * Calculate the duration between two dates.
     *
     * @param string|null $start
     * @param string|null $end
     * @return Carbon|null
     */
    private function calculateDuration(?string $start, ?string $end): DateInterval|null
    {
        if (!$start || !$end) {
            return null;
        }

       // Return the difference between the two dates as a Carbon instance
        return Carbon::parse($start)->diff(Carbon::parse($end));
    }
}
