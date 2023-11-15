<?php

namespace Feature\Services;

use App\Enums\TimeRecordType;
use App\Models\Employee;
use App\Models\TimeRecord;
use App\Services\TimeRecordOrganiserService;
use App\Services\TimeRecordService;
use App\Services\TimeRecordStatService;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\MockObject\Exception;
use Tests\TestCase;


class TimeRecordServiceTest extends TestCase
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
     * Test the getTimeRecordsForDate method
     * @throws Exception
     */
    public function test_get_time_records_for_date_on_multi_day()
    {
        $employee = $this->standard_employee;

        // Create some time records we want to be returned
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2023-04-15 12:00:00',
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2023-04-15 13:00:00',
        ]);
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2023-04-15 23:00:00',
        ]);

        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2023-04-16 02:00:00',
        ]);

        // Create some extra time records we don't want to be returned
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_in',
            'recorded_at' => '2023-04-18 15:00:00',
        ]);
        TimeRecord::create([
            'employee_id' => $employee->id,
            'type' => 'clock_out',
            'recorded_at' => '2023-04-18 16:00:00',
        ]);

        // Create a TimeRecordService and mock the dependencies
        $timeRecordService = new TimeRecordService(
            $this->createMock(TimeRecordStatService::class),
            $this->createMock(TimeRecordOrganiserService::class)
        );

        // Call the method we are testing
        $timeRecords = $timeRecordService->getTimeRecordsForDate($employee->id, '2023-04-15');

        $expected = [
            [
                'employee_id' => $employee->id,
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => '2023-04-15 12:00:00',
            ],
            [
                'employee_id' => $employee->id,
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => '2023-04-15 13:00:00',
            ],
            [
                'employee_id' => $employee->id,
                'type' => TimeRecordType::CLOCK_IN,
                'recorded_at' => '2023-04-15 23:00:00',
            ],
            [
                'employee_id' => $employee->id,
                'type' => TimeRecordType::CLOCK_OUT,
                'recorded_at' => '2023-04-16 02:00:00',
            ],
        ];

        // Remove the unwanted fields from the records
        $actual = $timeRecords->map(function ($record) {
            return [
                'employee_id' => $record->employee_id,
                'type' => $record->type,
                'recorded_at' => $record->recorded_at->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        // Assert the correct records are returned
        $this->assertEquals($expected, $actual);





    }
}
