#!/bin/bash
set -e

# Crear base de datos SQLite si no existe
touch /var/www/html/database/database.sqlite

# Permisos
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chown www-data:www-data /var/www/html/database/database.sqlite

# Migraciones y seeders
php /var/www/html/artisan migrate --force
php /var/www/html/artisan db:seed --force

# Caché de configuración y rutas
php /var/www/html/artisan config:cache
php /var/www/html/artisan route:cache

# Iniciar Apache
exec apache2-foreground