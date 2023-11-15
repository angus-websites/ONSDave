<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Facades\EmployeeAuth;
use App\Http\Resources\TotalWorkedForDayResource;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class TodayController extends Controller
{
    private TimeRecordService $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    /**
     * Show the today page, where the employee can clock in/out
     * we also show the total time worked today etc
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $employee = EmployeeAuth::employee();
        $employee_id = $employee->id;

        $isClockedIn = $this->timeRecordService->isClockedIn($employee_id);

        $canSpecifyClockTime = $employee->can('specifyClockTime', TimeRecord::class);

        $timeWorkedToday = new TotalWorkedForDayResource($this->timeRecordService->getTimeWorkedForDate($employee_id, Carbon::today()));

        return Inertia::render('Today', ['isClockedIn' => $isClockedIn, 'canSpecifyClockTime' => $canSpecifyClockTime, 'timeWorkedToday' => $timeWorkedToday]);
    }
}
