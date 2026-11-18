<?php

use Illuminate\Support\Facades\Artisan;

Artisan::command('inventory:about', function () {
    $this->comment('Inventario WM funciona con Laravel y SQLite.');
})->purpose('Muestra información del sistema de inventario');
