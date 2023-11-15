<?php

namespace App\Http\Controllers;

use App\Facades\EmployeeAuth;
use App\Http\Resources\TimeRecordByMonthResource;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Carbon\Carbon;
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

        // Get the first day of the current month
        $currentMonth = Carbon::today()->firstOfMonth();

        $timeRecords = $this->timeRecordService->getTimeRecordsForMonth($employeeId, $currentMonth);

        $timeRecordsThisMonth = new TimeRecordByMonthResource($timeRecords, today());

        return Inertia::render('History', [
            'monthSessions' => $timeRecordsThisMonth,
        ]);
    }
}
