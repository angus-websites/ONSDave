<?php

namespace App\Http\Resources;

use App\Services\TimeRecordOrganiserService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimeRecordByDayResource extends ResourceCollection
{
    protected Carbon $date;
    protected TimeRecordOrganiserService $timeRecordOrganizerService;

    public function __construct(Collection $resource, Carbon $date, TimeRecordOrganiserService $timeRecordOrganizerService)
    {
        parent::__construct($resource);
        $this->date = $date;
        $this->timeRecordOrganizerService = $timeRecordOrganizerService;
    }

    public function toArray(Request $request): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'sessions' => $this->timeRecordOrganizerService->organiseRecordsByDay($this->collection, $this->date),
        ];
    }
}
