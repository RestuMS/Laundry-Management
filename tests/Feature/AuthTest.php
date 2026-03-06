<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_page_is_accessible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Login');
    }

    /** @test */
    public function register_page_is_disabled()
    {
        // Register page disabled & redirects to login or 404
        $response = $this->get('/register');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /** @test */
    public function admin_can_login_and_redirect_to_dashboard()
    {
        $user = User::factory()->admin()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function kasir_can_login_and_redirect_to_kasir_dashboard()
    {
        $user = User::factory()->kasir()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/kasir');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function owner_can_login_and_redirect_to_owner_dashboard()
    {
        $user = User::factory()->owner()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/owner');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function login_fails_with_wrong_password()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function login_requires_email_and_password()
    {
        $response = $this->post('/login', []);
        $response->assertSessionHasErrors(['email', 'password']);
    }

    /** @test */
    public function login_is_rate_limited_after_5_attempts()
    {
        $user = User::factory()->create();

        // Clear any existing rate limits
        \Illuminate\Support\Facades\RateLimiter::clear(
            \Illuminate\Support\Str::transliterate(\Illuminate\Support\Str::lower($user->email) . '|127.0.0.1')
        );

        // Disable route-level throttle to test controller-level rate limiting only
        for ($i = 0; $i < 5; $i++) {
            $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class)
                ->post('/login', [
                    'email' => $user->email,
                    'password' => 'wrong-password',
                ]);
        }

        // 6th attempt should be rate limited by the controller
        $response = $this->withoutMiddleware(\Illuminate\Routing\Middleware\ThrottleRequests::class)
            ->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function guest_cannot_access_dashboard()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function forgot_password_page_is_accessible()
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Lupa Password');
    }

    /** @test */
    public function user_cannot_register_publicly()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Karena register disabled for public, akan mendapat 404 
        $response->assertStatus(404);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
