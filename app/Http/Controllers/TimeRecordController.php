<?php

namespace App\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TimeRecordController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'clock_time' => 'sometimes|date',
        ]);

        $employee = Auth::user()->employee;
        $employee_id = $employee->id;
        $today = Carbon::today();

        $a = $employee->can('canSpecifyClockTime', TimeRecord::class);


        $clockTime = ($employee->can('canSpecifyClockTime', TimeRecord::class) && isset($validatedData['clock_time']))
            ? Carbon::parse($validatedData['clock_time'])
            : Carbon::now();


        $latestRecord = TimeRecord::where('employee_id', $employee_id)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (! $latestRecord || $latestRecord->type === TimeRecordType::CLOCK_OUT) {
            // If there's no record for today or the latest is a clock-out, then create a clock-in
            TimeRecord::create([
                'employee_id' => $employee_id,
                'recorded_at' => $clockTime,
                'type' => TimeRecordType::CLOCK_IN,
            ]);
        } else {
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
