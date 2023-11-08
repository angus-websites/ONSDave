<?php

namespace Tests\Unit\Services;

use App\DTOs\Session;
use App\Enums\TimeRecordType;
use App\Http\Resources\TimeRecordByMonthResource;
use DateInterval;
use PHPUnit\Framework\TestCase;
use App\Services\TimeRecordOrganiserService;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimeRecordOrganiserServiceByMonthTest extends TestCase
{

    /**
     * Test the service with multiple records on different days
     * in the same month
     */
    public function test_organise_by_month(): void
    {
        // Generate some fake records
        $records = new Collection([
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-15 17:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-16 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-16 17:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-17 09:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-17 13:00:00'),
            ],
            (object) [
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => Carbon::parse('2023-04-17 14:00:00'),
            ],
            (object) [
                'type' =>  TimeRecordType::AUTO_CLOCK_OUT,
                'recorded_at' => Carbon::parse('2023-04-17 15:00:00'),
            ],
        ]);

        // Create a new instance of the service
        $service = new TimeRecordOrganiserService();

        // Month to test
        $month = Carbon::parse('2023-04');

        // Call the method we want to test
        $monthSessions = $service->organiseRecordsByMonth($records, $month);

        // Assert the month is correct
        $this->assertEquals('2023-04', $monthSessions->getM);

        // Assert the month is correct
        $this->assertEquals('2023-04', $resourceResult['month']);

        // Assert the 15th day is correct
        $dayOne = $resourceResult['days'][14]; // 14 because the array is zero indexed
        $this->assertEquals('2023-04-15', $dayOne['date']);
        $this->assertEquals('2023-04-15 09:00:00', $dayOne['sessions'][0]['clock_in']);
        $this->assertEquals('2023-04-15 13:00:00', $dayOne['sessions'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayOne['sessions'][0]['duration']);
        $this->assertFalse($dayOne['sessions'][0]['ongoing']);
        $this->assertFalse($dayOne['sessions'][0]['auto_clock_out']);
        $this->assertEquals('2023-04-15 14:00:00', $dayOne['sessions'][1]['clock_in']);
        $this->assertEquals('2023-04-15 17:00:00', $dayOne['sessions'][1]['clock_out']);
        $this->assertEquals('03:00:00', $dayOne['sessions'][1]['duration']);
        $this->assertFalse($dayOne['sessions'][1]['ongoing']);
        $this->assertFalse($dayOne['sessions'][1]['auto_clock_out']);

        // Assert the 16th day is correct
        $dayTwo = $resourceResult['days'][15]; // 15 because the array is zero indexed
        $this->assertEquals('2023-04-16', $dayTwo['date']);
        $this->assertEquals('2023-04-16 09:00:00', $dayTwo['sessions'][0]['clock_in']);
        $this->assertEquals('2023-04-16 17:00:00', $dayTwo['sessions'][0]['clock_out']);
        $this->assertEquals('08:00:00', $dayTwo['sessions'][0]['duration']);
        $this->assertFalse($dayTwo['sessions'][0]['ongoing']);
        $this->assertFalse($dayTwo['sessions'][0]['auto_clock_out']);

        // Assert the 17th day is correct
        $dayThree = $resourceResult['days'][16]; // 16 because the array is zero indexed
        $this->assertEquals('2023-04-17', $dayThree['date']);
        $this->assertEquals('2023-04-17 09:00:00', $dayThree['sessions'][0]['clock_in']);
        $this->assertEquals('2023-04-17 13:00:00', $dayThree['sessions'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayThree['sessions'][0]['duration']);
        $this->assertFalse($dayThree['sessions'][0]['ongoing']);
        $this->assertFalse($dayThree['sessions'][0]['auto_clock_out']);
        $this->assertEquals('2023-04-17 14:00:00', $dayThree['sessions'][1]['clock_in']);
        $this->assertEquals('2023-04-17 15:00:00', $dayThree['sessions'][1]['clock_out']);
        $this->assertEquals('01:00:00', $dayThree['sessions'][1]['duration']);
        $this->assertFalse($dayThree['sessions'][1]['ongoing']);
        $this->assertTrue($dayThree['sessions'][1]['auto_clock_out']);

    }
}
