# Despliegue de GymX en Ubuntu Server

La configuración Nginx de referencia está en `deploy/nginx/gymx.conf`. Usa PHP-FPM 8.3; ajusta el socket y `server_name` al servidor. Laravel requiere PHP 8.3 o superior y que Nginx apunte a `public/`.

## Preparación del servidor

Instala Nginx, PHP-FPM con las extensiones requeridas por Laravel/PDO MySQL, MariaDB o MySQL, Composer, Node.js y npm. Mantén SSH actualizado y permite por firewall únicamente SSH, HTTP y HTTPS. Crea una base de datos y un usuario dedicados para GymX; no uses la cuenta `root` desde la aplicación.

## Publicar

1. Copia el proyecto a `/var/www/gymx` y configura permisos de lectura para el usuario de Nginx. Solo `storage/` y `bootstrap/cache/` necesitan escritura del servidor web.
2. Copia `.env.example` como `.env`; define `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL`, credenciales MySQL y `GYMX_ADMIN_USERNAME`, `GYMX_ADMIN_EMAIL` y `GYMX_ADMIN_PASSWORD` con una contraseña fuerte.
3. Ejecuta `composer install --no-dev --optimize-autoloader`, `npm ci`, `npm run build`, `php artisan key:generate`, `php artisan migrate --force` y `php artisan db:seed --force`. Guarda de forma segura las credenciales iniciales.
4. Ejecuta `php artisan storage:link`; configura el host con `deploy/nginx/gymx.conf`, valida con `sudo nginx -t` y recarga Nginx y PHP-FPM.
5. Habilita HTTPS antes de iniciar sesión en producción. phpMyAdmin y Webmin son herramientas administrativas opcionales: no las expongas públicamente; restrínge su acceso por VPN, firewall o túnel SSH.

El despliegue debe ejecutarse en el servidor destino: este repositorio no contiene credenciales ni conexión a una máquina Ubuntu. La configuración sigue la guía de [despliegue de Laravel](https://laravel.com/docs/13.x/deployment) y la [guía de Nginx de Ubuntu](https://ubuntu.com/server/docs/how-to-install-nginx/).
