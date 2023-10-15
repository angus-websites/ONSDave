<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    const CLOCK_IN = 'clock_in';
    const CLOCK_OUT = 'clock_out';

    protected $fillable = [
        'employee_id', 'recorded_at', 'type', 'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
