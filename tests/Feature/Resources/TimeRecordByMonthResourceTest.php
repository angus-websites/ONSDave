<?php

namespace Tests\Feature\Resources;

use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimeRecordByMonthResourceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->standard_employee = Employee::factory()->withRole('employee')->create();
        $this->restricted_employee = Employee::factory()->withRole('employee restricted')->create();

    }

    public function test_resource_fetch_by_month_endpoint_works()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 13:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-15 14:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::AUTO_CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-15 18:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-16 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-16 13:00:00'),
        ]);

        // Make an HTTP request to the month endpoint
        $response = $this->post(route('api.sessions.month', ['month' => '4', 'year' => '2023']));

        // Assert the response is correct
        $response->assertStatus(200);

        // Assert the 15th day is correct
        $dayOne = $response->json('data.days')[14]; // 14 because the array is zero indexed
        $this->assertEquals('2023-04-15', $dayOne['date']);
        $this->assertEquals('2023-04-15 09:00:00', $dayOne['sessions'][0]['clock_in']);
        $this->assertEquals('2023-04-15 13:00:00', $dayOne['sessions'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayOne['sessions'][0]['duration']);
        $this->assertFalse($dayOne['sessions'][0]['ongoing']);
        $this->assertFalse($dayOne['sessions'][0]['auto_clock_out']);
        $this->assertEquals('2023-04-15 14:00:00', $dayOne['sessions'][1]['clock_in']);
        $this->assertEquals('2023-04-15 18:00:00', $dayOne['sessions'][1]['clock_out']);
        $this->assertEquals('04:00:00', $dayOne['sessions'][1]['duration']);
        $this->assertFalse($dayOne['sessions'][1]['ongoing']);
        $this->assertTrue($dayOne['sessions'][1]['auto_clock_out']);

        // Assert the 16th day is correct
        $dayTwo = $response->json('data.days')[15]; // 15 because the array is zero indexed
        $this->assertEquals('2023-04-16', $dayTwo['date']);
        $this->assertEquals('2023-04-16 09:00:00', $dayTwo['sessions'][0]['clock_in']);
        $this->assertEquals('2023-04-16 13:00:00', $dayTwo['sessions'][0]['clock_out']);
        $this->assertEquals('04:00:00', $dayTwo['sessions'][0]['duration']);
        $this->assertFalse($dayTwo['sessions'][0]['ongoing']);
        $this->assertFalse($dayTwo['sessions'][0]['auto_clock_out']);

        // Assert the 17th day is empty
        $dayThree = $response->json('data.days')[16]; // 16 because the array is zero indexed
        $this->assertEquals('2023-04-17', $dayThree['date']);
        $this->assertEmpty($dayThree['sessions']);

    }

    /**
     * Test multi-day sessions work correctly in month resource
     */
    public function test_resource_fetch_by_month_for_multi_day_session()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Insert some records into the database
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => Carbon::parse('2023-04-14 09:00:00'),
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => TimeRecordType::CLOCK_OUT,
            'recorded_at' => Carbon::parse('2023-04-17 09:00:00'),
        ]);

        // Make an HTTP request to the desired endpoint
        $response = $this->post(route('api.sessions.month', ['month' => '4', 'year' => '2023']));

        // Assert day 14 is correct
        $dayOne = $response->json('data.days')[13]; // 13 because the array is zero indexed
        $this->assertEquals('2023-04-14', $dayOne['date']);

        $expectedSession = [
            'clock_in' => '2023-04-14 09:00:00',
            'clock_out' => '2023-04-17 09:00:00',
            'duration' => '72:00:00',
            'ongoing' => false,
            'auto_clock_out' => false,
            'duration_in_seconds' => 259200,
            'multi_day' => true,
        ];

        $this->assertEquals($expectedSession, $dayOne['sessions'][0]);



    }
}
