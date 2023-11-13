<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Facades\EmployeeAuth;
use App\Models\TimeRecord;
use App\Services\TimeRecordOrganiserService;
use App\Services\TimeRecordStatService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class TodayController extends Controller
{
    private TimeRecordStatService $timeRecordStatService;
    private TimeRecordOrganiserService $timeRecordOrganiserService;

    public function __construct()
    {
        $this->timeRecordStatService = new TimeRecordStatService();
        $this->timeRecordOrganiserService = new TimeRecordOrganiserService();
    }

    public function index(Request $request)
    {
        $employee = EmployeeAuth::employee();
        $employee_id = $employee->id;
        $today = Carbon::today();

        // Get the latest time record for the user for today
        $latestRecord = TimeRecord::where('employee_id', $employee_id)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $isClockedIn = ($latestRecord && $latestRecord->type === TimeRecordType::CLOCK_IN);

        $canSpecifyClockTime = $employee->can('specifyClockTime', TimeRecord::class);

        // TODO this is a duplicate of the code in HistoryController.php put into a service
        $timeRecords = TimeRecord::whereDate('recorded_at', $today)
            ->where('employee_id', $employee_id)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $timeWorkedToday = $this->timeRecordStatService->calculateTotalTimeWorkedForDay(
            $this->timeRecordOrganiserService->organiseRecordsByDay(
                $timeRecords,
                $today
            )
        );

        return Inertia::render('Today', ['isClockedIn' => $isClockedIn, 'canSpecifyClockTime' => $canSpecifyClockTime, 'timeWorkedToday' => $timeWorkedToday]);
    }
}
