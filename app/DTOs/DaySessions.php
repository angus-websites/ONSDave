<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use JsonSerializable;

class DaySessions implements JsonSerializable
{
    /**
     * @param Carbon $date [Date]
     * @param Collection $sessions [Session]
     */
    public function __construct(
        public Carbon $date,
        public Collection $sessions,
    ){}

    /**
     * Get the sessions for this day
     * @return Collection
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public static function fromArray(array $data): DaySessions
    {
        return new DaySessions(
            $data['date'],
            $data['sessions'],
        );
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('Y-m-d'),
            'sessions' => $this->sessions,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}

