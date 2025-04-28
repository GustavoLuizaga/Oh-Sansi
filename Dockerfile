# Usa la imagen oficial de PHP 7.4 con FPM
FROM php:7.4-fpm

# Instalar dependencias necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip \
    && docker-php-ext-configure zip \
    && docker-php-ext-install zip pdo_mysql \
    && rm -rf /var/lib/apt/lists/*  # Limpia la caché de APT

# Copiar el proyecto al contenedor
WORKDIR /var/www
COPY . .

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Nginx
RUN apt-get update && apt-get install -y nginx \
    && rm -rf /var/lib/apt/lists/*

# Copiar la configuración de Nginx al contenedor
COPY ./nginx/default.conf /etc/nginx/sites-available/default

# Exponer el puerto 80 para Nginx
EXPOSE 80

# Iniciar PHP-FPM y Nginx
CMD service nginx start && php-fpm -F
