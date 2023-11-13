<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TotalWorkedTodayResource extends JsonResource
{


    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Return the hours, minutes and seconds worked today
        return [
            'hours' => $this->resource["hours"],
            'minutes' => $this->resource["minutes"],
            'seconds' => $this->resource["seconds"],
        ];
    }
}
