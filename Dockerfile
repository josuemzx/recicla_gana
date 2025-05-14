FROM php:8.2-apache

# Instala PDO y el driver de PostgreSQL
RUN docker-php-ext-install pdo pdo_pgsql

# Habilita mod_rewrite y headers en Apache
RUN a2enmod rewrite headers

# Copia todo el contenido de public/ a la carpeta raíz de Apache
COPY public/ /var/www/html/

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
