# Ejecutar InventarioWM con Docker

Esta configuración está pensada para desarrollo local y conserva el motor SQLite del proyecto. No reemplaza ni modifica la lógica de inventario o autenticación.

## Requisitos

- Docker Desktop instalado y en ejecución.
- Un archivo `.env` válido en la raíz del proyecto.
- La clave `APP_KEY` y las variables de acceso del administrador configuradas en ese `.env`.

## Preparar el entorno

1. Copia `.env.example` a `.env` si todavía no tienes un archivo de entorno:

   **PowerShell**
   ```powershell
   Copy-Item .env.example .env
   ```

2. Genera la clave de Laravel si `APP_KEY` está vacía:

   Primero construye la imagen y solicita una clave sin modificar archivos dentro del contenedor:

   ```powershell
   docker compose build
   docker compose run --rm app php artisan key:generate --show
   ```

   Copia la clave que imprime el comando y pégala en la variable `APP_KEY` del archivo `.env` de Windows. No compartas esa clave.

3. Configura `INVENTORY_ADMIN_USERNAME` y `INVENTORY_ADMIN_PASSWORD_HASH` en `.env`. Genera el hash con:

   ```powershell
   docker compose run --rm app php artisan inventory:password "tu_clave_segura"
   ```

   Copia el hash resultante en `INVENTORY_ADMIN_PASSWORD_HASH`. El hash no es la contraseña que se escribe en el formulario. No compartas ni subas `.env`.

4. Comprueba que el archivo `database/database.sqlite` exista. Si no existe, créalo:

   ```powershell
   New-Item database/database.sqlite -ItemType File
   ```

   Si ya tienes una base SQLite con información, conserva ese archivo; no lo reemplaces.

## Construir e iniciar

Desde la raíz del repositorio:

```powershell
docker compose up -d --build
```

Abre [http://localhost:8081](http://localhost:8081).

Consulta el estado y los registros:

```powershell
docker compose ps
docker compose logs -f app
```

## Base de datos

El directorio local `database/` se monta dentro del contenedor. Por tanto, el archivo `database/database.sqlite` existente se reutiliza y los cambios de datos permanecen en el equipo.

Si es una instalación nueva y la base de datos todavía no tiene las tablas de la aplicación, ejecuta las migraciones una sola vez:

```powershell
docker compose exec app php artisan migrate --force
```

No ejecutes `migrate:fresh` sobre una base que contenga información: ese comando elimina las tablas existentes.

## Detener y volver a iniciar

```powershell
docker compose down
docker compose up -d
```

Para reconstruir la imagen después de cambiar el Dockerfile:

```powershell
docker compose up -d --build
```

No uses `docker compose down -v` si deseas conservar el volumen de almacenamiento de Laravel. La base SQLite está en el directorio local `database/`.

## Notas

- Apache sirve la aplicación desde `public/`, no desde la raíz del repositorio.
- El puerto local es `8081`; se puede cambiar en la sección `ports` de `compose.yaml`.
- La caché y las sesiones usan archivos, evitando requerir tablas de caché o sesiones.
- Esta configuración es para desarrollo local. Antes de publicar en Internet se debe preparar un entorno de producción: `APP_DEBUG=false`, secretos seguros, HTTPS, política de respaldos y revisión de permisos.
