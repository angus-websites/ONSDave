<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Models\TimeRecord;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $employeeId = EmployeeAuth::employee()->id;

        $currentMonth = today()->month;
        $currentYear = today()->year;

        $timeRecords = TimeRecord::whereMonth('recorded_at', $currentMonth)
            ->whereYear('recorded_at', $currentYear)
            ->where('employee_id', $employeeId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $timeRecordsThisMonth = new TimeRecordByMonthResource($timeRecords, today());

        return Inertia::render('History', [
            'monthSessions' => $timeRecordsThisMonth,
        ]);
    }
}
