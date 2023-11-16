<?php

namespace Feature\Http\Controllers;

use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SessionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->standard_employee = Employee::factory()->withRole('employee')->create();
        $this->restricted_employee = Employee::factory()->withRole('employee restricted')->create();

    }

    /**
     * Test fetch day sessions
     */
    public function testFetchDaySessions()
    {

        $this->actingAs($this->standard_employee->user);

        // Mock current date
        Carbon::setTestNow('2021-01-01');

        TimeRecord::create([
            'employee_id' => $this->standard_employee->id,
            'type' => TimeRecordType::CLOCK_IN,
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

        $response = $this->post(route('api.sessions.day', ['date' => '2021-01-01']));

        // Expected json should be date, and sessions
        $expected = [
            'data' => [
                'date' => '2021-01-01',
                'sessions' => [
                    [
                        "clock_in" => "2021-01-01 09:00:00",
                        "clock_out" => null,
                        "duration" => null,
                        "duration_in_seconds" => 0,
                        "ongoing" => true,
                        "auto_clock_out" => false,
                        "multi_day" => false
                    ]
                ],
            ]
        ];

        $response->assertJson($expected);

    }

}
