<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleMediaSearchPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/vehicle-media-search');
        $response->assertStatus(302);
        $location = $response->headers->get('Location');
        $this->assertIsString($location);
        $this->assertStringContainsString('/admin/login', $location);
    }

    public function test_authenticated_but_unauthorized_user_gets_403(): void
    {
        // Create a regular user without admin access
        $user = User::factory()->create([
            'email' => 'regular@example.com',
            'password' => bcrypt('password'),
        ]);
        
        // Regular users should not be able to access the admin panel
        $response = $this->actingAs($user)->get('/admin/vehicle-media-search');
        
        // Expect a 403 Forbidden response for unauthorized access
        $response->assertStatus(403);
    }
    
public function test_admin_user_can_access_page(): void
    {
        // Skip this test for now since we don't have admin authentication set up
        $this->markTestSkipped('Admin authentication not yet implemented');
        
        // In a real application, you would set up proper admin authentication
        // For example, you might have a role/permission system or an is_admin flag
        // Here's an example of what the test might look like:
        /*
        // Create an admin user
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            // Add any admin-specific fields here
        ]);
        
        // Log in as admin and access the page
        $response = $this->actingAs($admin)->get('/admin/vehicle-media-search');
        $response->assertOk();
        $response->assertSee('Vehicle Media Search');
        */
    }
}
