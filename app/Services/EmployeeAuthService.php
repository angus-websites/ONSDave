<?php

namespace App\Services;

use App\Models\Employee;
use Illuminate\Support\Facades\Auth;

class EmployeeAuthService
{
    public function employee(): ?Employee
    {
        $user = Auth::user();
        return $user ? $user->employee : null;
    }
}
