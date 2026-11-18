# Inventario WM

Aplicación web para administrar el inventario de una tienda pequeña. Está construida con **Laravel 12, PHP 8.2 o superior y SQLite**. Funciona directamente en Windows, Linux o macOS con PHP y SQLite.

## Funcionalidades

- Inicio de sesión para el administrador con contraseña almacenada mediante hash.
- Protección contra intentos repetidos de inicio de sesión.
- Registro, edición y eliminación protegida de productos.
- Búsqueda por nombre o categoría y paginación.
- Registro de entradas y salidas con actualización transaccional del stock.
- Validación para impedir salidas superiores a las existencias disponibles.
- Alertas de stock bajo y resumen del valor del inventario.
- Historial completo con filtros por producto, tipo y fecha; cada movimiento nuevo conserva el usuario administrador que lo registró.
- Reportes por categoría y exportación a CSV con protección contra fórmulas de hoja de cálculo.
- Interfaz adaptable a computadores, tabletas y celulares.

## Tecnologías y requisitos

- PHP **8.2 o superior**.
- Extensiones PHP: `pdo_sqlite`, `sqlite3`, `openssl`, `mbstring`, `tokenizer`, `xml`, `dom`, `xmlwriter`, `ctype`, `fileinfo` y `json`.
- SQLite (incluido como extensión de PHP; no necesitas instalar un servidor de base de datos).
- Composer 2 es necesario para instalar las dependencias después de clonar el repositorio. La carpeta `vendor/` no se versiona.

## Instalación en Windows con XAMPP

1. Instala o abre XAMPP y verifica que su versión de PHP sea 8.2 o superior. Abre PowerShell dentro de la carpeta del proyecto.
2. Comprueba PHP y las extensiones necesarias:

   ```powershell
   php -v
   php -m | findstr /I "pdo_sqlite sqlite3 mbstring openssl dom xml xmlwriter"
   ```

   Si `php` no se reconoce, usa la ruta del PHP de XAMPP en los comandos, por ejemplo `C:\xampp\php\php.exe`. Si no aparecen `pdo_sqlite` y `sqlite3`, abre `C:\xampp\php\php.ini`, habilita las líneas `extension=pdo_sqlite` y `extension=sqlite3` (quitando `;` al inicio si lo tienen) y verifica que `mbstring`, `dom`, `xml` y `xmlwriter` también estén activas. Guarda el archivo y abre una nueva terminal.

3. Instala las dependencias del proyecto. El repositorio no incluye la carpeta `vendor/` porque está excluida de Git:

   ```powershell
   composer install
   ```

4. Crea tu configuración local a partir de la plantilla:

   ```powershell
   Copy-Item .env.example .env
   php artisan key:generate
   ```

   El archivo `.env` contiene claves y credenciales privadas. No lo compartas ni lo subas a Git.

5. Comprueba que exista `database/database.sqlite`. Si no existe, créalo:

   ```powershell
   New-Item database/database.sqlite -ItemType File
   ```

   **No reemplaces ese archivo si ya contiene tus productos o movimientos.**

6. Configura el usuario y la contraseña de administrador. Define el usuario en `INVENTORY_ADMIN_USERNAME` dentro de `.env`. Luego genera el hash con el siguiente comando; la contraseña se solicita de forma oculta:

   ```powershell
   php artisan inventory:password
   ```

   Copia el hash que aparece en `INVENTORY_ADMIN_PASSWORD_HASH` dentro de `.env`. Usa una contraseña de al menos 8 caracteres. El hash no es la contraseña que escribirás en el formulario.

7. Ejecuta las migraciones para crear las tablas que falten:

   ```powershell
   php artisan migrate
   ```

   Laravel no elimina los datos existentes al ejecutar `migrate`. No uses `migrate:fresh` en una base de datos que quieras conservar.

8. Inicia el servidor local:

   ```powershell
   php artisan serve
   ```

9. Abre [http://127.0.0.1:8000](http://127.0.0.1:8000) en tu navegador e inicia sesión con las credenciales configuradas.

### Si usas XAMPP y el comando `php` no funciona

Puedes ejecutar Artisan indicando el ejecutable de PHP de XAMPP. Por ejemplo:

```powershell
C:\xampp\php\php.exe artisan serve
```

Mantén abierta esa terminal mientras utilizas el sistema. Para detener el servidor, presiona `Ctrl + C`.

## Instalación en Linux o macOS

```bash
cp .env.example .env
php artisan key:generate
```

Ejecuta `composer install` únicamente si no tienes la carpeta `vendor/`.

Crea `database/database.sqlite` solo si no existe, configura las credenciales en `.env`, y después ejecuta:

```bash
php artisan inventory:password
php artisan migrate
php artisan serve
```

Abre `http://127.0.0.1:8000`.

## Pruebas

Con las dependencias y extensiones instaladas, ejecuta:

```bash
php artisan test
```

Las pruebas usan SQLite en memoria, por lo que no modifican la base de datos local del proyecto.

## Estructura principal

```text
InventarioWM/
├── app/
│   ├── Console/Commands/       # Comandos Artisan propios
│   ├── Http/Controllers/       # Controladores
│   ├── Http/Middleware/        # Protección de rutas
│   └── Models/                 # Modelos Eloquent
├── bootstrap/                  # Arranque de Laravel
├── config/                     # Configuración
├── database/
│   ├── migrations/             # Estructura de la base de datos
│   └── database.sqlite         # Base de datos local
├── docs/                       # Documentación del proyecto
├── legacy/flask/               # Prototipo anterior, conservado como referencia
├── public/css/                 # Estilos de la interfaz
├── resources/views/            # Vistas Blade
├── routes/                     # Rutas web y de consola
├── storage/                    # Archivos temporales y registros de Laravel
├── tests/                      # Pruebas automatizadas
├── .env.example                # Plantilla de configuración
├── artisan
└── composer.json
```

## Notas de seguridad y mantenimiento

- `.env` y las bases SQLite locales están excluidos de Git. Conserva una copia de seguridad periódica de `database/database.sqlite`.
- La aplicación se entrega configurada para desarrollo local (`APP_ENV=local`, `APP_DEBUG=true`). Antes de publicarla en Internet debes configurar producción, desactivar el modo debug, habilitar HTTPS y revisar usuarios, permisos y copias de seguridad.
- El prototipo Flask de `legacy/flask/` es histórico y no interviene en el funcionamiento de la aplicación Laravel.

## Mejoras que quedan para una siguiente etapa

El proyecto todavía no cubre todos los requisitos propuestos en el documento maestro. Para mantener claro el alcance, quedan pendientes el catálogo independiente de categorías y proveedores, los códigos únicos de producto, los usuarios con roles diferenciados y los ajustes de inventario con saldo anterior y nuevo. El acceso actual utiliza una sola cuenta administradora configurada en `.env`; no existe todavía un módulo para crear cuentas desde la interfaz.
