<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\TimeRecord;

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

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(Employee $user, TimeRecord $timeRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $user, TimeRecord $timeRecord): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $user, TimeRecord $timeRecord): bool
    {
        return false;
    }
}

