<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeRecordCollection;
use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $userId = Auth::user()->employee->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', now())
            ->where('employee_id', $userId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        $timeRecordsResource = new TimeRecordCollection($timeRecords);

        return Inertia::render('History', [
            'timeRecords' => $timeRecordsResource
        ]);
    }

    public function fetchByDate(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
        ]);

        $userId = Auth::user()->employee->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', $data['date'])
            ->where('employee_id', $userId)
            ->orderBy('recorded_at', 'asc')
            ->get();

        return new TimeRecordCollection($timeRecords);
    }

}

