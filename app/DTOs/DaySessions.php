<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DaySessions
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
}

