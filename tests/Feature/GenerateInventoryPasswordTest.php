<?php

namespace Tests\Feature;

use Tests\TestCase;

class GenerateInventoryPasswordTest extends TestCase
{
    public function test_password_is_requested_without_echoing_it_and_a_hash_is_generated(): void
    {
        $this->artisan('inventory:password')
            ->expectsQuestion('Escribe la contraseña del administrador', 'secure-password')
            ->expectsOutputToContain('INVENTORY_ADMIN_PASSWORD_HASH')
            ->expectsOutputToContain('$2y$')
            ->assertExitCode(0);
    }

    public function test_password_shorter_than_eight_characters_is_rejected(): void
    {
        $this->artisan('inventory:password')
            ->expectsQuestion('Escribe la contraseña del administrador', 'short')
            ->expectsOutputToContain('La contraseña debe tener al menos 8 caracteres.')
            ->assertExitCode(1);
    }
}
