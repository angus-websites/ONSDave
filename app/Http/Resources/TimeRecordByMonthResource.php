<?php

namespace App\Http\Resources;

use App\Services\TimeRecordOrganiserService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class TimeRecordByMonthResource extends ResourceCollection
{
    protected Carbon $month;
    protected TimeRecordOrganiserService $timeRecordOrganizerService;

    public function __construct($resource, Carbon $month, TimeRecordOrganiserService $timeRecordOrganizerService)
    {
        parent::__construct($resource);
        $this->month = $month;
        $this->timeRecordOrganizerService = $timeRecordOrganizerService;
    }

    public function toArray(Request $request): array
    {
        $organizedRecords = $this->timeRecordOrganizerService->organiseRecordsByMonth($this->resource, $this->month);

        return [
            'month' => $this->month->format('Y-m'),
            'days' => $organizedRecords,
        ];
    }
}
