<?php

namespace App\Enums;

enum TimeRecordType: string
{
    case CLOCK_IN = 'clock_in';
    case CLOCK_OUT = 'clock_out';
    case AUTO_CLOCK_OUT = 'auto_clock_out';
}
