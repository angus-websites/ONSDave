<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
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
