<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeAuthService
{
    public function employee(): ?Employee
    {
        $user = Auth::user();

        // Throw a 403 if the user is not an employee
        if (!$user?->employee) {
            abort(403);
        }

        // Otherwise return the employee
        return $user->employee;
    }
}
