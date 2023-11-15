<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Inertia\Inertia;

class HistoryController extends Controller
{

    private TimeRecordService $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function index()
    {
        $employeeId = EmployeeAuth::employee()->id;

        $currentMonth = today()->month;
        $currentYear = today()->year;

        $timeRecords = $this->timeRecordService->getTimeRecordsForMonth($employeeId, $currentMonth, $currentYear);

        $timeRecordsThisMonth = new TimeRecordByMonthResource($timeRecords, today());

        return Inertia::render('History', [
            'monthSessions' => $timeRecordsThisMonth,
        ]);
    }
}
