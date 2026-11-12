# Inventario WM

Sistema web para administrar el inventario de una tienda pequeña. La aplicación activa está construida con **Laravel 12, PHP 8.2 o superior y SQLite**.

El administrador puede registrar y editar productos, controlar entradas y salidas, consultar alertas de existencias bajas, revisar movimientos y exportar el inventario a CSV.

## Tecnologías

- Laravel 12
- PHP 8.2+
- SQLite
- Blade para las vistas
- CSS servido directamente desde `public/css`

## Estructura del proyecto

```text
InventarioWM/
├── app/
│   ├── Console/Commands/       # Comandos Artisan propios
│   ├── Http/
│   │   ├── Controllers/        # Controladores de la aplicación
│   │   └── Middleware/         # Protección de rutas
│   ├── Models/                 # Modelos Eloquent
│   └── Providers/              # Proveedores de servicios
├── bootstrap/
│   ├── app.php                 # Registro de rutas y middleware
│   └── cache/                  # Caché generada por Laravel
├── config/                     # Configuración de Laravel y del inventario
├── database/
│   ├── factories/              # Fábricas para datos de prueba
│   ├── migrations/             # Estructura de la base de datos
│   └── seeders/                # Datos iniciales o de demostración
├── docs/                       # Documentación del proyecto
├── legacy/
│   └── flask/                  # Prototipo anterior, conservado como referencia
├── public/
│   ├── css/                    # Hoja de estilos pública
│   └── index.php               # Punto de entrada web de Laravel
├── resources/views/            # Plantillas Blade
├── routes/
│   ├── console.php             # Rutas de consola
│   └── web.php                 # Rutas web
├── storage/                    # Archivos, sesiones, vistas compiladas y logs
├── tests/
│   ├── Feature/                # Pruebas de flujos de usuario
│   └── TestCase.php            # Clase base de pruebas
├── .env.example
├── artisan
├── composer.json
└── phpunit.xml
```

### Criterios de organización

- La lógica web se mantiene en `app/Http/Controllers`, los datos en `app/Models` y las plantillas en `resources/views`.
- Las validaciones actuales permanecen en los controladores porque el MVP tiene formularios pequeños y reglas puntuales. Se pueden extraer a `app/Http/Requests` cuando crezcan o se reutilicen.
- Los estilos permanecen en `public/css/app.css` porque se sirven directamente con `asset('css/app.css')`; el proyecto todavía no utiliza un flujo de compilación Vite.
- El prototipo Flask está archivado en `legacy/flask/`. No participa en el arranque ni en las rutas de Laravel.
- Las carpetas de ejecución de Laravel incluyen archivos `.gitignore` para conservar su estructura sin subir sesiones, cachés, logs ni otros datos locales.

## Requisitos

- PHP 8.2 o superior con las extensiones habituales de Laravel, incluyendo PDO SQLite, OpenSSL, Mbstring, Tokenizer, XML, Ctype, JSON y Fileinfo.
- Composer 2.

## Instalación local

1. Clona el repositorio y entra en la carpeta del proyecto.
2. Instala las dependencias:

```bash
composer install
```

3. Crea el archivo de entorno y genera la clave de Laravel.

En Windows PowerShell:

```powershell
Copy-Item .env.example .env
php artisan key:generate
```

En Linux o macOS:

```bash
cp .env.example .env
php artisan key:generate
```

4. Crea el archivo de SQLite si todavía no existe.

En Windows PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File
```

En Linux o macOS:

```bash
touch database/database.sqlite
```

5. Ejecuta las migraciones:

```bash
php artisan migrate
```

6. Configura el acceso de administrador. Genera un hash para la contraseña:

```bash
php artisan inventory:password "tu_clave_segura"
```

Copia el hash que imprime el comando en `INVENTORY_ADMIN_PASSWORD_HASH` dentro de `.env`. Define también `INVENTORY_ADMIN_USERNAME` con el usuario que utilizarás. No compartas ni subas el archivo `.env`.

7. Inicia la aplicación:

```bash
php artisan serve
```

Abre http://127.0.0.1:8000.

## Ejecutar las pruebas

Con las dependencias instaladas, ejecuta:

```bash
php artisan test
```

La configuración de PHPUnit utiliza SQLite en memoria para que las pruebas no modifiquen la base de datos local del proyecto.

## Funcionalidades actuales

- Acceso de administrador mediante sesión y contraseña almacenada como hash.
- Registro, edición y eliminación protegida de productos.
- Búsqueda por nombre o categoría y paginación.
- Registro transaccional de entradas y salidas.
- Validación para impedir salidas superiores al stock disponible.
- Alertas y métricas de existencias bajas.
- Historial de movimientos.
- Reporte por categoría y exportación del inventario a CSV.

## Nota sobre el prototipo anterior

La primera versión del proyecto se realizó con Flask. Sus archivos se conservaron en `legacy/flask/` para consulta histórica, pero la aplicación vigente utiliza Laravel. El punto de entrada web es `public/index.php` y el comando de desarrollo es `php artisan serve`.
