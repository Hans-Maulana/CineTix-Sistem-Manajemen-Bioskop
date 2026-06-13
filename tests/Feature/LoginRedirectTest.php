<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_back_to_booking_page_when_redirect_param_set(): void
    {
        $role = Role::create(['name' => 'customer']);
        $user = User::create([
            'role_id' => $role->id,
            'name' => 'Member',
            'email' => 'member@example.com',
            'password' => bcrypt('password'),
            'contact' => '08123456789',
        ]);

        $bookingUrl = '/booking/schedule/5';

        $this->get(route('login', ['redirect' => $bookingUrl]));

        $response = $this->post(route('login'), [
            'email' => 'member@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect($bookingUrl);
    }
}
