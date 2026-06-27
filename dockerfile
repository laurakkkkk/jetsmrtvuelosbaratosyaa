FROM php:8.2-apache

# Habilitar mod_rewrite para URLs amigables
RUN a2enmod rewrite

# Instalar extensiones necesarias
RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
    && docker-php-ext-install zip

# Copiar todos los archivos del proyecto al servidor
COPY . /var/www/html/

# Dar permisos
RUN chown -R www-data:www-data /var/www/html/

# Exponer el puerto 10000 (que Render usa)
EXPOSE 10000

# Iniciar Apache en el puerto 10000
CMD ["apache2-foreground"]
