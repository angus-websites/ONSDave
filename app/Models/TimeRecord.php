<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id', 'recorded_at', 'type', 'notes'
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
