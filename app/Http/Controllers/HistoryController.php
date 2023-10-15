<?php

namespace App\Http\Controllers;

use App\Http\Resources\TimeRecordCollection;
use App\Models\TimeRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $userId = Auth::user()->employee->id;

        $timeRecords = TimeRecord::whereDate('recorded_at', $date)
            ->where('employee_id', $userId)
            ->get();

        $timeRecordsResource = new TimeRecordCollection($timeRecords);

        return Inertia::render('History', [
            'timeRecords' => $timeRecordsResource
        ]);
    }
}
