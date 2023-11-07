<?php

namespace App\Http\Controllers;

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
        $userId = Auth::user()->employee->id;
        $today = Carbon::today();

        $latestRecord = TimeRecord::where('employee_id', $userId)
            ->whereDate('recorded_at', $today)
            ->orderBy('recorded_at', 'desc')
            ->first();

        if (! $latestRecord || $latestRecord->type === TimeRecord::CLOCK_OUT) {
            // If there's no record for today or the latest is a clock-out, then create a clock-in
            TimeRecord::create([
                'employee_id' => $userId,
                'recorded_at' => Carbon::now(),
                'type' => TimeRecord::CLOCK_IN,
            ]);
        } else {
            // Otherwise, create a clock-out
            TimeRecord::create([
                'employee_id' => $userId,
                'recorded_at' => Carbon::now(),
                'type' => 'clock_out',
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
