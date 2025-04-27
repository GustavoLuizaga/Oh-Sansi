# Usa PHP 7.4 como base
FROM php:7.4-fpm

# Instala extensiones necesarias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Crear carpeta de la app
WORKDIR /var/www

# Copiar todos los archivos del proyecto
COPY . .

# Instalar dependencias de Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Asignar permisos
RUN chown -R www-data:www-data /var/www && chmod -R 755 /var/www

# Puerto por donde se expondrá
EXPOSE 8000

# Comando para correr Laravel usando php artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
