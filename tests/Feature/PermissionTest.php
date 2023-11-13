<?php

namespace Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Employee;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    /** setup */
    public function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
    }

    /**
     * Test an employee with no roles cannot do any operation on the time records
     */
    public function test_employee_with_no_roles_cannot_access_any_time_record_actions(): void
    {
        $employee = Employee::factory()->create();

        // Assert that the employee does not have the permission to view time records
        $this->assertFalse($employee->hasPermissionTo('time_records.create'));
        $this->assertFalse($employee->hasPermissionTo('time_records.read'));
        $this->assertFalse($employee->hasPermissionTo('time_records.update'));
        $this->assertFalse($employee->hasPermissionTo('time_records.delete'));
    }

    /**
     * Test an employee with the employee role can CRUD time records
     */
    public function test_employee_with_employee_role_can_view_time_records(): void
    {
        $employee = Employee::factory()->withRole('employee')->create();

        // Assert that the employee has the permission to view time records
        $this->assertTrue($employee->hasPermissionTo('time_records.create'));
        $this->assertTrue($employee->hasPermissionTo('time_records.read'));
        $this->assertTrue($employee->hasPermissionTo('time_records.update'));
        $this->assertTrue($employee->hasPermissionTo('time_records.delete'));
    }
}
