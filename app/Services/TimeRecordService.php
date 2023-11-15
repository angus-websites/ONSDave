<?php

namespace App\Services;

use App\Enums\TimeRecordType;
use App\Http\Resources\TotalWorkedForDayResource;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeRecordService
{
    protected TimeRecordStatService $timeRecordStatService;
    protected TimeRecordOrganiserService $timeRecordOrganiserService;

    public function __construct(TimeRecordStatService $timeRecordStatService, TimeRecordOrganiserService $timeRecordOrganiserService)
    {
        $this->timeRecordStatService = $timeRecordStatService;
        $this->timeRecordOrganiserService = $timeRecordOrganiserService;
    }

    /**
     * Determine if the employee is clocked in or not
     */
    public function isClockedIn($employeeId): bool
    {
        $latestRecord = TimeRecord::where('employee_id', $employeeId)
            ->whereDate('recorded_at', Carbon::today())
            ->orderBy('recorded_at', 'desc')
            ->first();

        return ($latestRecord && $latestRecord->type === TimeRecordType::CLOCK_IN);
    }

    /**
     * Get the total time worked for the given date, returns an array of hours, minutes and seconds
     */
    public function getTimeWorkedForDate($employeeId, $date): array
    {
        // TODO write specific test for this method
        $timeRecords = $this->getTimeRecordsForDate($employeeId, $date);

        return $this->timeRecordStatService->calculateTotalTimeWorkedForDay(
            $this->timeRecordOrganiserService->organiseRecordsByDay($timeRecords, $date)
        );
    }

    public function getTimeWorkedToday($employeeId): array
    {
        return $this->getTimeWorkedForDate($employeeId, Carbon::today());
    }

    /**
     * Will fetch the TimeRecords for the given date and employee, if the last record is a clock in, it will fetch the
     * first record of the next day and append it to the collection
     */
    public function getTimeRecordsForDate($employeeId, $date): \Illuminate\Database\Eloquent\Collection
    {
        // Fetch records for the specified date
        $dateRecords = TimeRecord::whereDate('recorded_at', $date)
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Check and append the first record of the next day if the last record is a clock in
        if ($dateRecords->isNotEmpty() && $dateRecords->last()->type === TimeRecordType::CLOCK_IN) {
            $nextDay = Carbon::parse($date)->addDay()->toDateString();
            $firstRecordNextDay = TimeRecord::whereDate('recorded_at', $nextDay)
                ->where('employee_id', $employeeId)
                ->orderBy('recorded_at', 'asc')
                ->first();

            if ($firstRecordNextDay && $firstRecordNextDay->type === TimeRecordType::CLOCK_OUT) {
                $dateRecords->push($firstRecordNextDay);
            }
        }

        return $dateRecords;
    }

    /**
     * Get the time records for a given month
     * the provided month should be a Carbon instance of the first day of the month
     */
    public function getTimeRecordsForMonth($employeeId, Carbon $month): \Illuminate\Support\Collection
    {
        // Go through every day of the month and fetch the time records for that day
        $days = $month->daysInMonth;
        $timeRecords = collect();

        for ($i = 1; $i <= $days; $i++) {
            // Create a Carbon instance for the current day based on the month and year
            $date = Carbon::createFromDate($month->year, $month->month, $i)->toDateString();
            $timeRecords = $timeRecords->merge($this->getTimeRecordsForDate($employeeId, $date));
        }

        return $timeRecords;
    }
}
