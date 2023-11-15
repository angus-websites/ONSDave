<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TodayControllerTest extends TestCase
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
     * Test we can get the today page as an employee
     */
    public function test_can_get_today_page_as_employee()
    {
        $this->actingAs($this->standard_employee->user)
            ->get(route('today'))
            ->assertStatus(200);
    }

    /**
     * Test we get the correct data when we get the today page as an employee
     */
    public function test_get_correct_data_when_get_today_page_as_standard_employee()
    {
        Carbon::setTestNow('2021-01-01 9:00:00');

        // Create a time record for today at 9am
        TimeRecord::create([
            'employee_id' => $this->standard_employee->id,
            'recorded_at' => '2021-01-01 9:00:00',
            'type' => 'clock_in',
        ]);

        // Call the endpoint
        $response = $this->actingAs($this->standard_employee->user)
            ->get(route('today'));

        // Expected time worked
        $expectedTimeWorked = [
            'hours' => '00',
            'minutes' => '00',
            'seconds' => '00',
        ];

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Today')
            ->where('isClockedIn', true)
            ->where('canSpecifyClockTime', true)
            ->where('timeWorkedToday', $expectedTimeWorked)
        );
    }

    /**
     * Test we get the correct data when we get the today page as an employee with restricted access
     */
    public function test_get_correct_data_when_get_today_page_as_restricted_employee()
    {
        Carbon::setTestNow('2021-01-01 9:00:00');

        // Create a time record for today at 9am
        TimeRecord::create([
            'employee_id' => $this->restricted_employee->id,
            'recorded_at' => '2021-01-01 9:00:00',
            'type' => 'clock_in',
        ]);

        // Clock out
        TimeRecord::create([
            'employee_id' => $this->restricted_employee->id,
            'recorded_at' => '2021-01-01 10:15:00',
            'type' => 'clock_out',
        ]);

        // Call the endpoint
        $response = $this->actingAs($this->restricted_employee->user)
            ->get(route('today'));

        // Expected time worked
        $expectedTimeWorked = [
            'hours' => '01',
            'minutes' => '15',
            'seconds' => '00',
        ];

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Today')
            ->where('isClockedIn', false)
            ->where('canSpecifyClockTime', false)
            ->where('timeWorkedToday', $expectedTimeWorked)
        );
    }
}
