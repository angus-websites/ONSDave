<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Facades\EmployeeAuth;
use App\Models\TimeRecord;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TimeRecordController extends Controller
{

    /**
     * Store a newly created resource in storage.
     * @throws AuthorizationException
     */
    public function store(Request $request)
    {
        $employee = EmployeeAuth::employee();

        // Authorise the employee to create a time record
        $this->authorizeForUser($employee, 'create', TimeRecord::class);

        // Validate the request data, default clockTimeManuallySet to true
        $validatedData = $request->validate([
            'clock_time' => 'sometimes|date',
        ]);


        $employee_id = $employee->id;
        $today = Carbon::today();

        // If the employee can specify the clock time and the clock time is set, then use that
        $clockTime = ($employee->can('canSpecifyClockTime', TimeRecord::class) && isset($validatedData['clock_time']))
            ? Carbon::parse($validatedData['clock_time'])
            : Carbon::now();

        // Get the latest time record for the user for today
        $latestRecord = TimeRecord::where('employee_id', $employee_id)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();


        // Check if there's no record for today or the latest is a clock-out
        if (! $latestRecord || $latestRecord->type === TimeRecordType::CLOCK_OUT) {

            // If the previous record is a clock-out, ensure the clock time is after the previous clock-out
            if ($latestRecord && $clockTime->isBefore($latestRecord->recorded_at)) {
                // Redirect back with an error
                return redirect()->back()->withErrors(['clock_time' => 'The clock in time must be after the previous clock out time']);
            }


            // If there's no record for today or the latest is a clock-out, then create a clock-in
            TimeRecord::create([
                'employee_id' => $employee_id,
                'recorded_at' => $clockTime,
                'type' => TimeRecordType::CLOCK_IN,
            ]);
        } else {

            // Ensure the clock time is after the latest record
            if ($clockTime->isBefore($latestRecord->recorded_at)) {
                // Redirect back with an error
                return redirect()->back()->withErrors(['clock_time' => 'The clock out time must be after the previous clock in time']);
            }

            // If clock out time is less than 5 seconds after previous clock in, delete the previous clock in
            if ($clockTime->diffInSeconds($latestRecord->recorded_at) <= 5) {
                $latestRecord->delete();

                // Redirect back with an info message and parameter to controller
                return redirect()->back()->with('info', 'As this session was less than 5 seconds, it was deleted.');
            }

            // Otherwise, create a clock-out
            TimeRecord::create([
                'employee_id' => $employee_id,
                'recorded_at' => $clockTime,
                'type' => TimeRecordType::CLOCK_OUT,
            ]);
        }

        return redirect()->route('today');  // Redirect back to the today view
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
