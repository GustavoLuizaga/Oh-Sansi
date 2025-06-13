#!/bin/bash
set -e

echo "🚀 Iniciando configuración de producción..."

# Configurar puerto dinámico para Nginx
export PORT=${PORT:-8080}
envsubst '${PORT}' < /etc/nginx/sites-available/default > /tmp/nginx.conf
mv /tmp/nginx.conf /etc/nginx/sites-available/default

# Esperar a que la base de datos esté disponible
echo "⏳ Esperando conexión a la base de datos..."
START_TIME=$(date +%s)
TIMEOUT=60

while ! php artisan migrate:status 2>/dev/null; do
    echo "⏳ Esperando conexión a BD..."
    sleep 3
    CURRENT_TIME=$(date +%s)
    if (( CURRENT_TIME - START_TIME > TIMEOUT )); then
        echo "❌ Timeout en la conexión a la BD"
        exit 1
    fi
done

echo "✅ Base de datos conectada exitosamente"

# Limpiar cache antes de empezar
echo "🧹 Limpiando cache existente..."
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true

# Ejecutar migrate:fresh con seeders (borra todo y recrea limpio)
echo "🗑️ Eliminando tablas existentes y recreando con seeders..."
php artisan migrate:fresh --seed --force

# Crear enlace simbólico para storage
echo "🔗 Creando enlace simbólico para storage..."
php artisan storage:link || true

# Optimizaciones para producción
echo "⚡ Aplicando optimizaciones de producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Asegurar permisos correctos
echo "🔒 Configurando permisos..."
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo "🌐 Iniciando PHP-FPM..."
php-fpm &

# Esperar a que PHP-FPM esté listo
echo "⏳ Esperando que PHP-FPM esté listo..."
while ! nc -z localhost 9000; do
  sleep 1
done
echo "✅ PHP-FPM está listo"

echo "🚀 Iniciando Nginx en puerto ${PORT}..."
nginx -g "daemon off;" &

# Esperar a que Nginx esté escuchando en el puerto correcto
echo "⏳ Esperando que Nginx esté listo..."
while ! nc -z localhost ${PORT}; do
  sleep 1
done
echo "✅ Nginx está escuchando en puerto ${PORT}"

# Verificar que la aplicación responde
echo "🏥 Verificando health check..."
sleep 5
if curl -f http://localhost:${PORT}/health; then
    echo "✅ Aplicación respondiendo correctamente"
else
    echo "⚠️ Health check falló, pero continuando..."
fi

echo "🎉 Aplicación iniciada exitosamente"

# Mantener el contenedor activo
wait