<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeRecordByDayResource;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->employee->id;

        $currentMonth = today()->month;
        $currentYear = today()->year;

        $timeRecords = TimeRecord::whereMonth('recorded_at', $currentMonth)
            ->whereYear('recorded_at', $currentYear)
            ->where('employee_id', $userId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $timeRecordsThisMonth = new TimeRecordByMonthResource($timeRecords, today());

        return Inertia::render('History', [
            'timeRecordsThisMonth' => $timeRecordsThisMonth
        ]);
    }

    public function fetchByDate(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $userId = Auth::user()->employee->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', $data['date'])
            ->where('employee_id', $userId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return new TimeRecordByDayResource($timeRecords, Carbon::parse($data['date']));
    }

    public function fetchByMonth(Request $request)
    {
        // Validate the provided month and year
        $validated = $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer',
        ]);

        // Create a Carbon instance from the provided month and year
        $date = Carbon::createFromDate($validated['year'], $validated['month'], 1);

        $userId = Auth::user()->employee->id;

        $timeRecords = TimeRecord::whereMonth('recorded_at', $validated['month'])
            ->whereYear('recorded_at', $validated['year'])
            ->where('employee_id', $userId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return new TimeRecordByMonthResource($timeRecords, $date);


    }

}

