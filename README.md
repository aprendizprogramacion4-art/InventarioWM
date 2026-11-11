# Inventario WM (Laravel)

Sistema web para organizar una tienda pequeña. Está construido con **Laravel 12, PHP 8.2+ y SQLite**. El administrador registra productos y entregas, controla entradas y salidas, revisa alertas de stock y descarga reportes CSV.

## Requisitos

- PHP 8.2 o superior con las extensiones habituales de Laravel (PDO SQLite, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON y Fileinfo).
- Composer 2.

## Instalación local

1. Clona el repositorio y entra a su carpeta.
2. Instala Laravel y sus dependencias:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

En Windows PowerShell, usa `Copy-Item .env.example .env` en lugar de `cp`.

3. Crea el archivo de la base de datos SQLite:

```bash
# Linux/macOS
 touch database/database.sqlite
# Windows PowerShell
 New-Item database/database.sqlite -ItemType File
```

4. Ejecuta las migraciones:

```bash
php artisan migrate
```

5. Configura una contraseña de administrador. Genera un hash y cópialo en `INVENTORY_ADMIN_PASSWORD_HASH` en `.env`:

```bash
php artisan inventory:password "cambia-esta-clave"
```

Configura también `INVENTORY_ADMIN_USERNAME=admin` (o el usuario que prefieras), guarda `.env` y limpia la configuración cacheada si la habías generado:

```bash
php artisan config:clear
```

6. Arranca la aplicación:

```bash
php artisan serve
```

Abre http://127.0.0.1:8000 e inicia sesión con los valores configurados en `.env`.

## Funcionalidades

- Acceso de administrador con sesión y contraseña guardada como hash.
- Alta, edición y eliminación protegida de productos.
- Búsqueda por nombre o categoría y paginación.
- Registro transaccional de entradas y salidas; impide salidas mayores que el stock disponible.
- Alertas y métricas de existencias bajas.
- Historial de movimientos.
- Reporte por categoría y exportación del inventario a CSV.

## Estructura principal

- `app/Http/Controllers`: controladores web y validaciones.
- `app/Http/Middleware`: protección de rutas del administrador.
- `app/Models`: modelos Eloquent.
- `database/migrations`: esquema SQLite.
- `resources/views`: páginas Blade.
- `routes/web.php`: rutas web.
- `public/css/app.css`: estilos.

## Nota sobre archivos anteriores

La rama de trabajo recibió una primera prueba en Flask antes de cambiar el enfoque. Los archivos antiguos `app.py`, `templates/` y `static/` se conservan en el historial/directorio por ahora, pero **no son utilizados por esta aplicación Laravel**. El punto de entrada actual es `public/index.php` y el comando es `php artisan serve`.
