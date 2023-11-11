<?php

namespace App\Policies;

use App\Models\Employee;

class TimeRecordPolicy
{

    /**
     * Should the user be able to specify the clock time?
     * @param Employee $employee
     * @return bool
     */
    public function canSpecifyClockTime(Employee $employee)
    {
        // Check the employee has the permission
        return $employee->hasPermissionTo('can specify clock time');
    }
}

