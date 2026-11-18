<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateInventoryPassword extends Command
{
    protected $signature = 'inventory:password {password? : Contraseña del administrador (opcional)}';
    protected $description = 'Genera un hash seguro para la contraseña del administrador';

    public function handle(): int
    {
        $password = $this->argument('password') ?: $this->secret('Escribe la contraseña del administrador');

        if (!is_string($password) || strlen($password) < 8) {
            $this->error('La contraseña debe tener al menos 8 caracteres.');

            return self::FAILURE;
        }

        $hash = Hash::make($password);
        $this->newLine();
        $this->info('Copia este hash en INVENTORY_ADMIN_PASSWORD_HASH dentro de .env:');
        $this->line($hash);
        $this->newLine();

        return self::SUCCESS;
    }
}
