# Dockerfile completo
FROM php:8.2-apache

# 1) Actualiza e instala los headers de libpq (PostgreSQL)
RUN apt-get update \
 && apt-get install -y --no-install-recommends libpq-dev \
 && rm -rf /var/lib/apt/lists/*

# 2) Compila e instala PDO_PGSQL (usa los headers que acabas de instalar)
RUN docker-php-ext-install pdo_pgsql

# 3) Habilita mod_rewrite y headers en Apache
RUN a2enmod rewrite headers

# 4) Copia tu carpeta public/ a la raíz de Apache
COPY public/ /var/www/html/

WORKDIR /var/www/html

EXPOSE 80
CMD ["apache2-foreground"]
