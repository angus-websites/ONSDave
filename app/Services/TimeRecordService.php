<?php
namespace App\Services;

use App\Models\TimeRecord;
use App\Http\Resources\TotalWorkedForDayResource;
use Carbon\Carbon;

class TimeRecordService
{
    protected TimeRecordStatService $timeRecordStatService;
    protected TimeRecordOrganiserService $timeRecordOrganiserService;

    public function __construct(TimeRecordStatService $timeRecordStatService, TimeRecordOrganiserService $timeRecordOrganiserService)
    {
        $this->timeRecordStatService = $timeRecordStatService;
        $this->timeRecordOrganiserService = $timeRecordOrganiserService;
    }

    public function getTimeWorkedForDate($employeeId, $date): TotalWorkedForDayResource
    {
        $timeRecords = $this->getTimeRecordsForDate($employeeId, $date);

        // TODO maybe wrap resources in the controller instead of here
        return new TotalWorkedForDayResource(
            $this->timeRecordStatService->calculateTotalTimeWorkedForDay(
                $this->timeRecordOrganiserService->organiseRecordsByDay($timeRecords, $date)
            )
        );
    }

    public function getTimeWorkedToday($employeeId): TotalWorkedForDayResource
    {
        return $this->getTimeWorkedForDate($employeeId, Carbon::today());
    }

    /**
     * Will fetch the TimeRecords for the given date and employee, if the last record is a clock in, it will fetch the
     * first record of the next day and append it to the collection
     */
    private function getTimeRecordsForDate($employeeId, $date)
    {
        // Fetch records for the specified date
        $dateRecords = TimeRecord::whereDate('recorded_at', $date)
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Check and append the first record of the next day if the last record is a clock in
        if ($dateRecords->isNotEmpty() && $dateRecords->last()->isClockIn()) {
            $nextDay = Carbon::parse($date)->addDay()->toDateString();
            $firstRecordNextDay = TimeRecord::whereDate('recorded_at', $nextDay)
                ->where('employee_id', $employeeId)
                ->orderBy('recorded_at', 'asc')
                ->first();

            if ($firstRecordNextDay && $firstRecordNextDay->isClockOut()) {
                $dateRecords->push($firstRecordNextDay);
            }
        }

        return $dateRecords;
    }

}
