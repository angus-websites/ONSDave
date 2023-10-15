<?php

namespace App\Http\Controllers;

use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class TodayController extends Controller
{

    public function index(Request $request)
    {
        $userId = Auth::user()->employee->id;
        $today = Carbon::today();

        // Get the latest time record for the user for today
        $latestRecord = TimeRecord::where('employee_id', $userId)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();

        $isClockedIn = ($latestRecord && $latestRecord->type === 'clock_in');

        return Inertia::render('Today', ['isClockedIn' => $isClockedIn]);
    }
}
