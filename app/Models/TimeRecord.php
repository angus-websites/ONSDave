<?php

namespace App\Models;

use App\Enums\TimeRecordType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TimeRecord extends Model
{
    protected $fillable = [
        'employee_id', 'recorded_at', 'type', 'notes',
    ];

    // Cast the 'type' attribute to enum
    protected $casts = [
        'type' => TimeRecordType::class,
        'recorded_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
