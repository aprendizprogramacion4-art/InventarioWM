<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_page_is_available_to_guests(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Inventario WM');
    }

    public function test_guest_is_redirected_to_login_when_opening_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_log_in_with_configured_credentials(): void
    {
        config([
            'inventory.admin_username' => 'admin',
            'inventory.admin_password_hash' => Hash::make('secret-password'),
        ]);

        $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'secret-password',
        ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('admin_authenticated', true);
    }

    public function test_invalid_credentials_do_not_authenticate_the_user(): void
    {
        config([
            'inventory.admin_username' => 'admin',
            'inventory.admin_password_hash' => Hash::make('secret-password'),
        ]);

        $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'incorrect-password',
        ])
            ->assertSessionHasErrors('username')
            ->assertSessionMissing('admin_authenticated');
    }

    public function test_admin_can_log_out(): void
    {
        $this->withSession(['admin_authenticated' => true])
            ->post(route('logout'))
            ->assertRedirect(route('login'))
            ->assertSessionMissing('admin_authenticated');
    }
}
