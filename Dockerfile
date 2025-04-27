# Usa la imagen oficial de PHP 7.4 con FPM
FROM php:7.4-fpm

# Instala las extensiones y herramientas necesarias
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    unzip \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install zip pdo_mysql

# Copia el código de tu proyecto al contenedor
COPY . /var/www

# Define el directorio de trabajo
WORKDIR /var/www

# Instala dependencias de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expone el puerto 9000 para PHP-FPM
EXPOSE 9000

# Comando por defecto para correr PHP-FPM
CMD ["php-fpm"]
