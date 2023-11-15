<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
use App\Http\Resources\TimeRecordByDayResource;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Http\Resources\TotalWorkedForDayResource;
use App\Models\TimeRecord;
use App\Services\TimeRecordOrganiserService;
use App\Services\TimeRecordService;
use App\Services\TimeRecordStatService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    private TimeRecordService $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    /**
     * Fetch all sessions for the current employee for the given date
     */
    public function fetchDaySessions(Request $request): TimeRecordByDayResource
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $employeeId = EmployeeAuth::employee()->id;

        $timeRecords = $this->timeRecordService->getTimeRecordsForDate($employeeId, $data['date']);

        return new TimeRecordByDayResource($timeRecords, Carbon::parse($data['date']));
    }

    /**
     * Fetch all sessions for the current employee for the given month
     */
    public function fetchMonthSessions(Request $request): TimeRecordByMonthResource
    {
        // Validate the provided month and year
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        // Create a Carbon instance from the provided month and year
        $date = Carbon::createFromDate($validated['year'], $validated['month'], 1);

        $employeeId = EmployeeAuth::employee()->id;

        // TODO move this to the service
        $timeRecords = TimeRecord::whereMonth('recorded_at', $validated['month'])
            ->whereYear('recorded_at', $validated['year'])
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return new TimeRecordByMonthResource($timeRecords, $date);

    }

    /**
     * Calculate the total time worked for a given day
     */
    public function calculateTotalWorkedTimeForDay(Request $request, TimeRecordStatService $timeRecordStatService, TimeRecordOrganiserService $timeRecordOrganiserService): TotalWorkedForDayResource
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $employeeId = EmployeeAuth::employee()->id;

        $timeRecords = $this->timeRecordService->getTimeRecordsForDate($employeeId, $data['date']);

        // Use the stat service to calculate the total time worked
        $timeWorkedToday = $timeRecordStatService->calculateTotalTimeWorkedForDay(
            $timeRecordOrganiserService->organiseRecordsByDay(
                $timeRecords,
                Carbon::parse($data['date'])
            )
        );

        return new TotalWorkedForDayResource($timeWorkedToday);
    }
}
