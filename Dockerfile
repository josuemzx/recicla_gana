# Usa la imagen oficial de PHP + Apache
FROM php:8.2-apache

# Instala PDO-MySQL para que PDO pueda hablar con MySQL
RUN docker-php-ext-install pdo_mysql

# Habilita los módulos de Apache que necesitas
RUN a2enmod rewrite headers

# Copia tu frontend al directorio raíz de Apache
COPY public/ /var/www/html/

EXPOSE 80
CMD ["apache2-foreground"]
