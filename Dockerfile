# Dockerfile para DESARROLLO
FROM php:8.1-fpm

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    netcat-openbsd \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Establecer directorio de trabajo
WORKDIR /var/www

# Copiar archivos de dependencias primero
COPY composer.json composer.lock ./
COPY package.json package-lock.json ./

# Instalar dependencias PHP sin scripts (evitar errores de artisan)
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Copiar TODO el código de la aplicación
COPY . .

# Ahora ejecutar los scripts de Composer (después de copiar todo)
RUN composer run-script post-autoload-dump

# Instalar dependencias Node.js
RUN npm install

# Crear directorios necesarios y establecer permisos
RUN mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    && mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Exponer puerto
EXPOSE 9000

CMD ["php-fpm"]