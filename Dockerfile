# Dockerfile
FROM php:8.2-apache

# 1) Instala PDO y el driver MySQL
RUN docker-php-ext-install pdo pdo_mysql

# 2) Habilita mod_rewrite y headers en Apache
RUN a2enmod rewrite headers

# 3) Copia todo el contenido de public/ a la carpeta raíz de Apache
COPY public/ /var/www/html/

# 4) Sitúa el WORKDIR donde están los .php
WORKDIR /var/www/html

# 5) Expón el puerto 80 y arranca Apache
EXPOSE 80
CMD ["apache2-foreground"]
