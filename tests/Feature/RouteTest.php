<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

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

    public function test_today_route_require_authentication()
    {
        // Check the 'today' route
        $response = $this->get('/today');
        $response->assertRedirect('/login');

        // Create a user for the test
        $user = User::factory()->create();

        // Check the 'today' route
        $response = $this->actingAs($user)->get('/today');
        $response->assertStatus(200);

    }

    public function test_history_route_requires_authentication()
    {
        // Check the 'history' route without authentication
        $response = $this->get('/history');
        $response->assertRedirect('/login');

        // Create a user for the test
        $user = User::factory()->create();

        // Check when authenticated
        $response = $this->actingAs($user)->get('/history');
        $response->assertStatus(200);
    }

}

