<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertEquals('customer', $user->role->name);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAdmin());
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_invalid_email_format_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => '2472020@maranatha',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'email tidak valid, gunakan email yang valid',
        ]);
        $this->assertGuest();
    }

    public function test_valid_email_with_tld_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User 2',
            'email' => '2472020@maranatha.ac.id',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $user = auth()->user();
        $this->assertEquals('customer', $user->role->name);
        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAdmin());
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
