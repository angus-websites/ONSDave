<?php

namespace App\Policies;

use App\Models\Employee;

class TimeRecordPolicy
{

    /**
     * Should the user be able to specify the clock time?
     * @param Employee $employee
     * @return mixed
     */
    public function canSpecifyClockTime(Employee $employee)
    {
        return true;
    }
}

