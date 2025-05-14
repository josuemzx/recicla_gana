# Usa la imagen oficial de PHP + Apache
FROM php:8.2-apache

# 1) Instala la extensión PDO MySQL
RUN docker-php-ext-install pdo_mysql

# 2) Habilita los módulos Apache que necesitas
RUN a2enmod rewrite headers

# 3) Copia todo el contenido de tu carpeta `public/` al directorio raíz de Apache
COPY public/ /var/www/html/

# Expon el puerto 80 y arranca Apache en primer plano
EXPOSE 80
CMD ["apache2-foreground"]
