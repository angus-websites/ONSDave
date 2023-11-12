<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Facades\EmployeeAuth;
use App\Models\TimeRecord;
use App\Services\TimeRecordStatService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class TodayController extends Controller
{
    private TimeRecordStatService $timeRecordStatService;

    public function __construct()
    {
        $this->timeRecordStatService = new TimeRecordStatService();
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

        return Inertia::render('Today', ['isClockedIn' => $isClockedIn, 'canSpecifyClockTime' => $canSpecifyClockTime ]);
    }
}
