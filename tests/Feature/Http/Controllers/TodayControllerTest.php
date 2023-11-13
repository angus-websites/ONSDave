<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Employee;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
