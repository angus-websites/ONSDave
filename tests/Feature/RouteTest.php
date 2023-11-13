<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_routes_are_working()
    {
        // List of all your application routes
        $routes = [
            '/',
            '/login',
            '/register',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_authenticated_routes_need_authentication()
    {

        // Seed Roles
        $this->seed(\Database\Seeders\RoleSeeder::class);

        // Check the 'today' route
        $response = $this->get('/today');
        $response->assertRedirect('/login');

        // Create a user for the test
        $user = Employee::factory()->create()->user;

        // TODO add check for user with no employee

        // Check the 'today' route
        $response = $this->actingAs($user)->get('/today');
        $response->assertStatus(200);

    }
}
