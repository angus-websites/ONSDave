<?php

namespace App\Http\Resources;

use App\Services\TimeRecordOrganizerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimeRecordByMonthResource extends ResourceCollection
{
    protected Carbon $month;
    protected TimeRecordOrganizerService $timeRecordOrganizerService;

    public function __construct($resource, Carbon $month, TimeRecordOrganizerService $timeRecordOrganizerService)
    {
        parent::__construct($resource);
        $this->month = $month;
        $this->timeRecordOrganizerService = $timeRecordOrganizerService;
    }

    public function toArray(Request $request): array
    {
        $organizedRecords = $this->timeRecordOrganizerService->organizeRecordsByMonth($this->resource, $this->month);

        return [
            'month' => $this->month->format('Y-m'),
            'days' => $organizedRecords,
        ];
    }
}
