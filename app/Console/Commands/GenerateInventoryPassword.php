<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateInventoryPassword extends Command
{
    protected $signature = 'inventory:password {password : Contraseña del administrador}';
    protected $description = 'Genera un hash seguro para la contraseña del administrador';

    public function handle(): int
    {
        $hash = Hash::make((string) $this->argument('password'));
        $this->line('Copia este hash en INVENTORY_ADMIN_PASSWORD_HASH dentro de .env:');
        $this->line($hash);

        return self::SUCCESS;
    }
}
