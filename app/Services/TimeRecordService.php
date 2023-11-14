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
        $timeRecords = TimeRecord::whereDate('recorded_at', $date)
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

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

}
