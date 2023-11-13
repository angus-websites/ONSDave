<?php

namespace App\Services;

use App\DTOs\DaySessions;

class TimeRecordStatService
{
    /**
     * Calculate the total hours worked for the day in hours, minutes, and seconds.
     */
    public function calculateTotalTimeWorkedForDay(DaySessions $daySessions): array
    {
        $total_seconds = 0;

        foreach ($daySessions->sessions as $session) {
            $total_seconds += $session->getDurationInSeconds();
        }

        // Calculate the total hours, minutes, and seconds
        $totalHours = (int) floor($total_seconds / 3600);
        $totalMinutes = (int) floor(floor($total_seconds / 60) % 60);
        $totalSeconds = (int) $total_seconds % 60;

        return [
            'hours' => $totalHours,
            'minutes' => $totalMinutes,
            'seconds' => $totalSeconds,
        ];
    }
}
