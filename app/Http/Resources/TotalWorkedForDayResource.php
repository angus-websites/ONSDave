<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TotalWorkedForDayResource extends JsonResource
{
    public static $wrap = null;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Return the hours, minutes and seconds worked today all as two digit strings
        return [
            'hours' => sprintf('%02d', $this->resource['hours']),
            'minutes' => sprintf('%02d', $this->resource['minutes']),
            'seconds' => sprintf('%02d', $this->resource['seconds']),
        ];
    }
}
