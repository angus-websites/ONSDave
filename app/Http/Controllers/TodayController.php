<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodayController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::user()->employee;
        $employee_id = $employee->id;
        $today = Carbon::today();

        // Get the latest time record for the user for today
        $latestRecord = TimeRecord::where('employee_id', $employee_id)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $isClockedIn = ($latestRecord && $latestRecord->type === TimeRecordType::CLOCK_IN);


        $canSpecifyClockTime = $employee->can('canSpecifyClockTime', TimeRecord::class);

        return Inertia::render('Today', ['isClockedIn' => $isClockedIn, 'canSpecifyClockTime' => $canSpecifyClockTime ]);
    }
}
