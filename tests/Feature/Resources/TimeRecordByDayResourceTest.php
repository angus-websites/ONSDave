<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\TimeRecordByDayResource;
use App\Models\Employee;
use App\Models\TimeRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Tests\TestCase;

class TimeRecordByDayResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the resource with four records
     * which should result in two sessions
     */
    public function test_resource_organises_four_records_with_auto_clock_out(): void
    {
        // Mockup some fake records
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
                'type' => 'auto_clock_out',
                'recorded_at' => ('2023-04-15 18:00:00')
            ]
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Assert the date is correct
        $this->assertEquals('2023-04-15', $resourceResult['date']);

        // Fetch the records from the resource
        $resourceResult = $resourceResult['records'];

        // Assert the records are correctly organized
        $this->assertEquals('2023-04-15 09:00:00', $resourceResult[0]['clock_in']);
        $this->assertEquals('2023-04-15 13:00:00', $resourceResult[0]['clock_out']);
        $this->assertEquals('04:00:00', $resourceResult[0]['duration']);
        $this->assertFalse($resourceResult[0]['ongoing']);
        $this->assertFalse($resourceResult[0]['auto_clock_out']);

        // Assert the next record is correctly organized
        $this->assertEquals('2023-04-15 14:00:00', $resourceResult[1]['clock_in']);
        $this->assertEquals('2023-04-15 18:00:00', $resourceResult[1]['clock_out']);
        $this->assertEquals('04:00:00', $resourceResult[1]['duration']);
        $this->assertFalse($resourceResult[1]['ongoing']);

    }

    /**
     * Test the resource with a single record
     * which should result in a single session that is ongoing
     */
    public function test_resource_organises_records_with_ongoing_session(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 09:00:00')
            ],
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Assert the date is correct
        $this->assertEquals('2023-04-15', $resourceResult['date']);

        // Fetch the records from the resource
        $resourceResult = $resourceResult['records'];

        // Assert the records are correctly organized
        $this->assertEquals('2023-04-15 09:00:00', $resourceResult[0]['clock_in']);
        $this->assertNull($resourceResult[0]['clock_out']);
        $this->assertNull($resourceResult[0]['duration']);
        $this->assertTrue($resourceResult[0]['ongoing']);

    }

    /**
     * Test the resource some missing data
     * the clock_out records are missing
     */
    public function test_resource_with_some_missing_data(): void
    {
        // Mockup some fake records
        $records = new Collection([
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 09:00:00')
            ],
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 14:00:00')
            ]
        ]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Assert the date is correct
        $this->assertEquals('2023-04-15', $resourceResult['date']);

        // Fetch the records from the resource
        $resourceResult = $resourceResult['records'];

        // Assert the records are correctly organized
        $this->assertEquals('2023-04-15 09:00:00', $resourceResult[0]['clock_in']);
        $this->assertNull($resourceResult[0]['clock_out']);
        $this->assertNull($resourceResult[0]['duration']);
        $this->assertTrue($resourceResult[0]['ongoing']);

        // Assert the next record is correctly organized
        $this->assertEquals('2023-04-15 14:00:00', $resourceResult[1]['clock_in']);
        $this->assertNull($resourceResult[1]['clock_out']);
        $this->assertNull($resourceResult[1]['duration']);
        $this->assertTrue($resourceResult[1]['ongoing']);

    }

    /**
     * Test the resource with no data
     */
    public function test_resource_with_no_data(): void
    {
        // Mockup some fake records
        $records = new Collection([]);

        // Create a date to pass to the resource
        $date = Carbon::parse('2023-04-15');

        // Pass the records to the resource
        $resourceResult = (new TimeRecordByDayResource($records, $date))->toArray(request());

        // Assert the date is correct
        $this->assertEquals('2023-04-15', $resourceResult['date']);

        // Fetch the records from the resource
        $resourceResult = $resourceResult['records'];

        // Assert the records are correctly organized
        $this->assertEmpty($resourceResult);
    }

}
