<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\TimeRecordByMonthResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TimeRecordByMonthResourceTest extends TestCase
{
    /**
     * Test the resource with multiple records on different days
     * in the same month
     */
    public function test_resource_organises_by_month(): void
    {
        // Generate some fake records
        $records = new Collection([
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 09:00:00')
            ],
            (object) [
                'type' => 'clock_out',
                'recorded_at' => ('2023-04-15 13:00:00')
            ],
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 14:00:00')
            ],
            (object) [
                'type' => 'clock_out',
                'recorded_at' => ('2023-04-15 17:00:00')
            ],
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-16 09:00:00')
            ],
            (object) [
                'type' => 'clock_out',
                'recorded_at' => ('2023-04-16 17:00:00')
            ],
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-17 09:00:00')
            ],
            (object) [
                'type' => 'clock_out',
                'recorded_at' => ('2023-04-17 13:00:00')
            ],
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-17 14:00:00')
            ],
            (object) [
                'type' => 'auto_clock_out',
                'recorded_at' => ('2023-04-17 15:00:00')
            ],
        ]);

        // Create the current month as a carbon instance to match the records
        $month = Carbon::parse('2023-04');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByMonthResource($records, $month))->toArray(request());

        // Assert the month is correct
        $this->assertEquals('2023-04', $resourceResult['month']);

        // Assert the 15th day is correct
        $dayOne = $resourceResult['days'][14]; // 14 because the array is zero indexed
        $this->assertEquals('2023-04-15', $dayOne['date']);
        $this->assertEquals('2023-04-15 09:00:00', $dayOne['records'][0]['clock_in']);
        $this->assertEquals('2023-04-15 13:00:00', $dayOne['records'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayOne['records'][0]['duration']);
        $this->assertFalse($dayOne['records'][0]['ongoing']);
        $this->assertFalse($dayOne['records'][0]['auto_clock_out']);
        $this->assertEquals('2023-04-15 14:00:00', $dayOne['records'][1]['clock_in']);
        $this->assertEquals('2023-04-15 17:00:00', $dayOne['records'][1]['clock_out']);
        $this->assertEquals('03:00:00', $dayOne['records'][1]['duration']);
        $this->assertFalse($dayOne['records'][1]['ongoing']);
        $this->assertFalse($dayOne['records'][1]['auto_clock_out']);

        // Assert the 16th day is correct
        $dayTwo = $resourceResult['days'][15]; // 15 because the array is zero indexed
        $this->assertEquals('2023-04-16', $dayTwo['date']);
        $this->assertEquals('2023-04-16 09:00:00', $dayTwo['records'][0]['clock_in']);
        $this->assertEquals('2023-04-16 17:00:00', $dayTwo['records'][0]['clock_out']);
        $this->assertEquals('08:00:00', $dayTwo['records'][0]['duration']);
        $this->assertFalse($dayTwo['records'][0]['ongoing']);
        $this->assertFalse($dayTwo['records'][0]['auto_clock_out']);

        // Assert the 17th day is correct
        $dayThree = $resourceResult['days'][16]; // 16 because the array is zero indexed
        $this->assertEquals('2023-04-17', $dayThree['date']);
        $this->assertEquals('2023-04-17 09:00:00', $dayThree['records'][0]['clock_in']);
        $this->assertEquals('2023-04-17 13:00:00', $dayThree['records'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayThree['records'][0]['duration']);
        $this->assertFalse($dayThree['records'][0]['ongoing']);
        $this->assertFalse($dayThree['records'][0]['auto_clock_out']);
        $this->assertEquals('2023-04-17 14:00:00', $dayThree['records'][1]['clock_in']);
        $this->assertEquals('2023-04-17 15:00:00', $dayThree['records'][1]['clock_out']);
        $this->assertEquals('01:00:00', $dayThree['records'][1]['duration']);
        $this->assertFalse($dayThree['records'][1]['ongoing']);
        $this->assertTrue($dayThree['records'][1]['auto_clock_out']);



    }
}
