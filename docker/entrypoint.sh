#!/bin/bash
set -e

# Esperar a que la base de datos esté disponible
until php artisan migrate:status 2>/dev/null; do
    echo "Esperando a que la base de datos esté disponible..."
    sleep 2
done

# Ejecutar migraciones y seeders
echo "Ejecutando migraciones..."
php artisan migrate --force

echo "Ejecutando seeders..."
php artisan db:seed --force

# Asegurar permisos correctos
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Iniciar PHP-FPM
exec "$@"