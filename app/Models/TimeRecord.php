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

    // Cast the 'type' attribute to your enum
    protected $casts = [
        'type' => TimeRecordType::class,
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    protected function type(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => TimeRecordType::from($value),
            set: fn (TimeRecordType $value) => $value->value,
        );
    }
}
