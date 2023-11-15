<?php

namespace Feature\Http\Controllers;

use App\Models\Employee;
use App\Models\TimeRecord;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class HistoryControllerTest extends TestCase
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
     *  Test that the history page is rendered correctly
     */
    public function test_history_page_is_rendered_correctly()
    {
        $employee = $this->standard_employee;

        $this->actingAs($employee->user);

        $response = $this->get(route('history'));

        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) =>
            $page->component('History')
        );
    }



}
