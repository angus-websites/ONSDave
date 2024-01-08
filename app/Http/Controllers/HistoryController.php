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

    /**
     * Show the history page.
     */
    public function index()
    {
        $employeeId = EmployeeAuth::employee()->id;

        // Get the first day of the current month
        $currentMonth = Carbon::today()->firstOfMonth();

        // Use the TimeRecordService to get time records for the current month
        $timeRecords = $this->timeRecordService->getTimeRecordsForMonth($employeeId, $currentMonth);

        // Create a TimeRecordByMonthResource to format the time records
        $timeRecordsThisMonth = new TimeRecordByMonthResource($timeRecords, today());

        return Inertia::render('History', [
            'monthSessions' => $timeRecordsThisMonth,
        ]);
    }
}
