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

    public function __construct($resource, Carbon $month)
    {
        parent::__construct($resource);
        $this->month = $month;
        $this->timeRecordOrganizerService = new TimeRecordOrganiserService();
    }

    public function toArray(Request $request): array
    {
        return $this->timeRecordOrganizerService->organiseRecordsByMonth($this->resource, $this->month)->toArray();
    }
}
