<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TimeRecordControllerTest extends TestCase
{
    use RefreshDatabase;

    /** setup */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);

        $this->standard_employee = Employee::factory()->withRole('employee')->create();
        $this->restricted_employee = Employee::factory()->withRole('employee restricted')->create();

    }

    /**
     * Test that the store method creates a clock-in record if there is no record for today
     */
    public function test_store_creates_clock_in_if_no_record_for_today()
    {
        $employee = $this->standard_employee;
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
        $employee = $this->standard_employee;
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
        $employee = $this->standard_employee;
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
     * Test when a clockTime is provided, but time zone is not, the default time zone is used
     */
    public function test_store_uses_default_timezone_if_timezone_not_provided_and_clock_time_provided()
    {
        $employee = $this->standard_employee;
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
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Clock in
        $response = $this->post(route('time-records.store', ['clock_time' => 'invalid']));

        // Check an error is returned
        $response->assertSessionHasErrors('clock_time');

    }



    /**
     * Test an employee that has necessary permissions that doesnt specify a clock time will use the current time
     */
    public function test_store_with_employee_with_necessary_permissions_uses_current_time_if_no_clock_time_specified()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Set the mock time to 9am
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Attempt clock in with a specified time
        $this->post(route('time-records.store'));

        // Check the database should ignore the specified time and use the current time
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

    }

    /**
     * Test that when a user provides a clock out time before the previous clock in time, and error is returned
     */
    public function test_store_when_clock_out_time_provided_is_before_previous_clock_in_time_error_returned()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in at 10am
        $this->post(route('time-records.store', ['clock_time' => '2021-01-01 10:00:00']));

        // Clock out at 9am
        $response = $this->post(route('time-records.store', ['clock_time' => '2021-01-01 9:00:00']));

        // Check an error is returned
        $response->assertSessionHasErrors('clock_time');

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);

    }

    /**
     * Test when a user provides a clock in time before the previous clock out time, an error is returned
     */
    public function test_store_when_clock_in_time_provided_is_before_previous_clock_out_time_error_returned()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in at 10am
        $this->post(route('time-records.store', ['clock_time' => '2021-01-01 10:00:00']));

        // Clock out at 12pm
        $response = $this->post(route('time-records.store', ['clock_time' => '2021-01-01 12:00:00']));

        // Clock in at 11am
        $response = $this->post(route('time-records.store', ['clock_time' => '2021-01-01 11:00:00']));

        // Check an error is returned
        $response->assertSessionHasErrors('clock_time');

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 11:00:00',
        ]);
    }

    /**
     * Test that when a user clocks out within 5 seconds of clocking in both records are deleted
     */
    public function test_store_when_clock_out_time_provided_is_within_5_seconds_of_previous_clock_in_time_both_records_deleted()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in at 10am
        $this->post(route('time-records.store', ['clock_time' => '2021-01-01 10:00:00']));

        // Clock out at 10am 5 seconds
        $response = $this->post(route('time-records.store', ['clock_time' => '2021-01-01 10:00:05']));

        // Assert the info message is returned
        $response->assertSessionHas('info');

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 10:00:00',
        ]);

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2021-01-01 10:00:05',
        ]);
    }

    /**
     * Test that a user without create permission cannot create a time record
     */
    public function test_store_when_employee_without_create_permission_cannot_create_time_record()
    {
        $employee = Employee::factory()->create();
        $this->actingAs($employee->user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in at 10am
        $response = $this->post(route('time-records.store'));

        $response->assertForbidden();

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);
    }

    /**
     * Test an error is thrown when a user without an employee record tries to clock in
     */
    public function test_store_when_user_without_employee_record_cannot_clock_in()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 09:00:00');

        // Clock in at 10am
        $response = $this->post(route('time-records.store'));

        $response->assertForbidden();

        // Check record is not created
        $this->assertDatabaseMissing('time_records', [
            'employee_id' => $user->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 09:00:00',
        ]);
    }

    /**
     * Test store with an unauthorized user will redirect to login
     */
    public function test_store_with_unauthorized_user_redirects_to_login()
    {
        $response = $this->post(route('time-records.store'));
        $response->assertRedirect(route('login'));
    }

    /**
     * Test when a user clocks out after midnight, the clock out is recorded the next day
     */
    public function test_store_when_clock_out_after_midnight_recorded_next_day()
    {
        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        // Mock the Carbon today method to return a specific date
        Carbon::setTestNow('2021-01-01 23:00:00');

        // Clock in at 11pm
        $this->post(route('time-records.store', ['clock_time' => '2021-01-01 23:00:00']));

        // Clock out at 1am the next day
        $this->post(route('time-records.store', ['clock_time' => '2021-01-02 01:00:00']));

        // Check both records are created
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2021-01-01 23:00:00',
        ]);

        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2021-01-02 01:00:00',
        ]);

    }

    /**
     * Test store with a clock time in a different timezone
     * here we test within and outside of daylight savings time
     */
    public function test_store_clock_time_conversion_with_dst()
    {
        // Date within DST period in London (e.g., July 1)
        $this->postAndCheckTime('2021-07-01 09:00:00', 'Europe/London', '2021-07-01 08:00:00');

        // Date outside DST period in London (e.g., November 1)
        $this->postAndCheckTime('2021-11-01 09:00:00', 'Europe/London', '2021-11-01 09:00:00');

    }

    /**
     * Test store with a clock time in a different timezone
     */
    public function test_store_clock_time_conversion_with_different_timezones()
    {
        // Test America/New_York timezone
        $this->postAndCheckTime('2021-07-01 09:00:00', 'America/New_York', '2021-07-01 13:00:00');

        // Test Asia/Tokyo timezone
        $this->postAndCheckTime('2021-07-01 09:00:00', 'Asia/Tokyo', '2021-07-01 00:00:00');

    }


    /**
     * Test store when local time-zone clock time is a different day to UTC
     */
    public function test_store_clock_time_timezone_conversion_with_different_days()
    {
        // Test Australia/Sydney timezone
        $this->postAndCheckTime('2021-07-01 09:00:00', 'Australia/Sydney', '2021-06-30 23:00:00');
    }


    /**
     * Test clock in with a range of different clock time formats
     */
    public function test_store_with_different_datetime_formats_and_timezones()
    {

        // Define different datetime formats, all same day at midnight 11/16/2023
        $testTimezones = [
            [
                'format' => 'm/d/Y H:i:s',
                'testDateTime' => '11/16/2023 00:00:00',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/Y H:i',
                'testDateTime' => '11/16/2023 00:00',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/Y H',
                'testDateTime' => '11/16/2023 00',
                'isValid' => false,
            ],
            [
                'format' => 'm/d/Y',
                'testDateTime' => '11/16/2023',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/y',
                'testDateTime' => '11/16/23',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/y H:i:s',
                'testDateTime' => '11/16/23 00:00:00',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/y H:i',
                'testDateTime' => '11/16/23 00:00',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/y H',
                'testDateTime' => '11/16/23 00',
                'isValid' => false,
            ],
            [
                'format' => 'm/d/y',
                'testDateTime' => '11/16/23',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/Y H:i:s',
                'testDateTime' => '11/16/2023 00:00:00',
                'isValid' => true,
            ],
            [
                'format' => 'm/d/Y H:i',
                'testDateTime' => '11/16/2023 00:00',
                'isValid' => true,
            ],
        ];

        // Mock the Carbon today method to 11/16/2023
        Carbon::setTestNow('2023-11-16 00:00:00');


        // The time used for testing is 2021-07-01 00:00:00
        $timezone = 'Europe/London';

        // The expected time in UTC
        $expectedInUTC = '2023-11-16 00:00:00';


        foreach ($testTimezones as $testTimezone) {
            echo 'Testing format: ' . $testTimezone['format'] . ' with timezone: ' . $timezone . ' and testDateTime: ' . $testTimezone['testDateTime'] . ' and expectedInUTC: ' . $expectedInUTC . ' and isValid: ' . $testTimezone['isValid'] . PHP_EOL;
            $this->postAndCheckTime($testTimezone['testDateTime'], $timezone, $expectedInUTC, $testTimezone['isValid'] === false);
        }

    }

    /**
     * Test an individual timezone
     */
    public function test_store_with_individual_timezone()
    {
        $this->postAndCheckTime('11/16/2023 20:57:17', 'Europe/London', '2023-11-16 20:57:17');
    }


    private function postAndCheckTime($localTime, $timezone, $expectedUTCTime, $shouldError=false): void
    {

        // Delete ALL time records
        DB::table('time_records')->delete();

        $employee = $this->standard_employee;
        $this->actingAs($employee->user);

        $response = $this->post(route('time-records.store'), [
            'clock_time' => $localTime,
            'type' => 'clock_in',
            'time_zone' => $timezone,
        ]);

        if ($shouldError) {

            // Check an error is raised
            $response->assertSessionHasErrors('clock_time');

            // Ensure the record is not created
            $this->assertDatabaseMissing('time_records', [
                'employee_id' => $employee->id,
                'type' => 'clock_in',
                'recorded_at' => $expectedUTCTime,
            ]);

            DB::rollBack();

            return;
        }

        // Check the record is created
        $this->assertDatabaseHas('time_records', [
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => $expectedUTCTime,
        ]);


    }




}
