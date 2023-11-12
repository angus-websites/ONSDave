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
            $seconds = $session->getDurationInSeconds();
            $total_seconds += $session->getDurationInSeconds();
        }

        // Calculate the total hours, minutes, and seconds

        // Get total hours as an integer
        $totalHours = floor($total_seconds / 3600);
        $totalMinutes = floor(($total_seconds / 60) % 60);
        $totalSeconds = $total_seconds % 60;

        return [
            'hours' => $totalHours,
            'minutes' => $totalMinutes,
            'seconds' => $totalSeconds,
        ];
    }

}
