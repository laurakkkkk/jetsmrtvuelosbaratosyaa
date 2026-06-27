FROM php:8.2-apache

RUN a2enmod rewrite

RUN apt-get update && apt-get install -y \
        libzip-dev \
        unzip \
    && docker-php-ext-install zip

# Copiar todos los archivos al servidor
COPY . /var/www/html/

# Configurar Apache para que sirva index.html por defecto
RUN echo "DirectoryIndex index.html index.php" >> /etc/apache2/apache2.conf

# Dar permisos
RUN chown -R www-data:www-data /var/www/html/

EXPOSE 10000

CMD ["apache2-foreground"]
