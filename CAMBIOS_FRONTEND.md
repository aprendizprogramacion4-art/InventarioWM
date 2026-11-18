# Cambios del front-end — Inventario WM

Se actualizó la presentación visual de la aplicación sin cambiar la lógica del inventario, los controladores, los modelos, las rutas ni la estructura de la base de datos.

## Dirección visual

- Paleta propia: verde tinta, marfil y mandarina.
- Tipografía y jerarquía editorial con títulos más expresivos y textos auxiliares discretos.
- Marca WM construida con CSS, sin imágenes ni dependencias externas.
- Panel principal con una ilustración geométrica de cajas, indicadores de inventario y accesos de operación.
- Formularios y tablas con mejor separación visual, estados más fáciles de identificar y focos accesibles.
- Acceso con una composición independiente de dos columnas.
- Adaptación para tabletas y celulares.

## Archivos actualizados

- `public/css/app.css`
- `resources/views/layouts/app.blade.php`
- `resources/views/auth/login.blade.php`
- `resources/views/dashboard.blade.php`
- `resources/views/products/edit.blade.php`
- `resources/views/movements/index.blade.php`
- `resources/views/reports/index.blade.php`

## Compatibilidad

Se conservaron los nombres de rutas y los campos enviados por cada formulario. No se agregaron librerías JavaScript, fuentes remotas ni imágenes externas: la identidad visual se dibuja con HTML y CSS y puede funcionar sin conexión a Internet.

## Nota sobre la configuración local

El archivo `.env` no se distribuye en este paquete porque contiene claves y datos privados. Conserva tu `.env` actual al copiar estos cambios sobre tu proyecto.
