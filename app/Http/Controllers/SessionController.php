<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
use App\Http\Resources\TimeRecordByDayResource;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Http\Resources\TotalWorkedTodayResource;
use App\Models\TimeRecord;
use App\Services\TimeRecordOrganiserService;
use App\Services\TimeRecordStatService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Fetch all sessions for the current employee for the given date
     * @param Request $request
     * @return TimeRecordByDayResource
     */
    public function fetchDaySessions(Request $request): TimeRecordByDayResource
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $employeeId = EmployeeAuth::employee()->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', $data['date'])
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return new TimeRecordByDayResource($timeRecords, Carbon::parse($data['date']));
    }

    /**
     * Fetch all sessions for the current employee for the given month
     * @param Request $request
     * @return TimeRecordByMonthResource
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
    public function calculateTotalWorkedTimeForDay(Request $request, TimeRecordStatService $timeRecordStatService, TimeRecordOrganiserService $timeRecordOrganiserService): TotalWorkedTodayResource
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $employeeId = EmployeeAuth::employee()->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', $data['date'])
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Use the stat service to calculate the total time worked
        $timeWorkedToday = $timeRecordStatService->calculateTotalTimeWorkedForDay(
            $timeRecordOrganiserService->organiseRecordsByDay(
                $timeRecords,
                Carbon::parse($data['date'])
            )
        );

        return new TotalWorkedTodayResource($timeWorkedToday);
    }
}