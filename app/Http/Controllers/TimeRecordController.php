<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Facades\EmployeeAuth;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimeRecordController extends Controller
{
    private TimeRecordService $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    /**
     * Covert the provided clock time to UTC
     * based on the user's time zone
     */
    private function convertToUtc($clockTime, $userTimeZone): Carbon
    {
        $userTimeZone = $userTimeZone ?? 'Europe/London';
        if (! in_array($userTimeZone, timezone_identifiers_list())) {
            $userTimeZone = 'Europe/London';
        }

        return Carbon::parse($clockTime, $userTimeZone)
            ->setTimezone('UTC');
    }

    /**
     * Store a new time record in the database
     *
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $employee = EmployeeAuth::employee();
        $this->authorizeForUser($employee, 'create', TimeRecord::class);

        $validatedData = $request->validate([
            'clock_time' => 'sometimes|date',
            'time_zone' => 'sometimes|string',
        ]);

        // If the employee can specify the clock time, use the provided clock time and convert it to UTC
        if ($employee->can('specifyClockTime', TimeRecord::class) && isset($validatedData['clock_time'])) {
            $clockTime = $this->convertToUtc($validatedData['clock_time'], $validatedData['time_zone'] ?? null);
        }
        // Otherwise, use the current time
        else {
            $clockTime = Carbon::now('UTC');
        }

        // Fetch the latest time record for the employee
        $latestRecord = $this->timeRecordService->getLatestTimeRecordForEmployee($employee->id);

        // Determine the type of time record
        $type = $latestRecord && $latestRecord->type === TimeRecordType::CLOCK_IN ? TimeRecordType::CLOCK_OUT : TimeRecordType::CLOCK_IN;


        // Validate clock time against the latest record
        if ($latestRecord && $clockTime->isBefore($latestRecord->recorded_at)) {
            $error = $type === TimeRecordType::CLOCK_IN ? 'The clock in time must be after the previous clock out time' : 'The clock out time must be after the previous clock in time';

            return redirect()->back()->withErrors(['clock_time' => $error]);
        }

        // Handle the short-duration session case
        if ($type === TimeRecordType::CLOCK_OUT && $clockTime->diffInSeconds($latestRecord->recorded_at) <= 5) {
            $latestRecord->delete();
            return redirect()->back()->with('info', 'As this session was less than 5 seconds, it was deleted.');
        }

        // Create the time record
        TimeRecord::create([
            'employee_id' => $employee->id,
            'recorded_at' => $clockTime,
            'type' => $type,
        ]);

        return redirect()->route('today');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
