<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Resources\TimeRecordCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class TimeRecordCollectionTransformTest extends TestCase
{


    /** @test */
    public function test_resource_correctly_organizes_clock_in_and_clock_out_entries()
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

        // Pass the records to the resource
        $resourceResult = (new TimeRecordCollection($records))->toArray(request());

        // Assert the resource correctly organized the records
        $this->assertEquals('2023-04-15 09:00:00', $resourceResult[0]['clock_in']);
        $this->assertEquals('2023-04-15 13:00:00', $resourceResult[0]['clock_out']);
        $this->assertEquals('04:00:00', $resourceResult[0]['duration']);
        $this->assertFalse($resourceResult[0]['ongoing']);

        $this->assertEquals('2023-04-15 14:00:00', $resourceResult[1]['clock_in']);
        $this->assertEquals('2023-04-15 18:00:00', $resourceResult[1]['clock_out']);
        $this->assertEquals('04:00:00', $resourceResult[1]['duration']);
        $this->assertFalse($resourceResult[1]['ongoing']);
    }

    /** @test */
    public function test_resource_handles_clock_in_without_a_corresponding_clock_out()
    {
        $records = new Collection([
            (object) [
                'type' => 'clock_in',
                'recorded_at' => ('2023-04-15 09:00:00')
            ]
        ]);

        $resourceResult = (new TimeRecordCollection($records))->toArray(request());

        $this->assertEquals('2023-04-15 09:00:00', $resourceResult[0]['clock_in']);
        $this->assertNull($resourceResult[0]['clock_out']);
        $this->assertNull($resourceResult[0]['duration']);
        $this->assertTrue($resourceResult[0]['ongoing']);
    }
}
