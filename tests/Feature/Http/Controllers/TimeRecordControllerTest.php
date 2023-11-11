<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimeRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the store method creates a clock-in record if there is no record for today
     */
    public function test_store_creates_clock_in_if_no_record_for_today()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Set the mock time to 9am
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in
        $this->post(route('time-records.store'));

        // Check the database contains a clock in
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

    }

    /**
     * Test that the store method creates a clock-out record if the latest record is a clock-in
     */
    public function test_store_creates_clock_out_if_latest_record_is_clock_in()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Set the mock time to 9am
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in
        $this->post(route('time-records.store'));

        // Set the mock time to 1pm
        Carbon::setTestNow('2021-01-01 13:00:00');

        // Clock out
        $this->post(route('time-records.store'));

        // Check the database contains a clock in before the clock out
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

        // Check the database contains a clock out
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2021-01-01 13:00:00',
        ]);

        // Reset the mocked time
        Carbon::setTestNow();
    }


    /**
     * Test when a clockTime is provided, it is used in the record
     */
    public function test_store_uses_clock_time_if_provided()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Clock in
        $this->post(route('time-records.store', ['clock_time' => '2021-01-01 09:00:00']));

        // Check the database contains a clock in before the clock out
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

    }

    /**
     * Test clock in with an invalid clock time
     */
    public function test_store_with_invalid_clock_time()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Clock in
        $response = $this->post(route('time-records.store', ['clock_time' => 'invalid']));

        // Check an error is returned
        $response->assertSessionHasErrors('clock_time');


    }


}
