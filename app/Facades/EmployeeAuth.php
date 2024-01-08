<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class EmployeeAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        /**
         * Used to fetch the singleton instance of the EmployeeAuth service
         */
        return 'employeeauth';
    }
}
