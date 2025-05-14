# Simple PHP + Apache
FROM php:8.2-apache
COPY public/ /var/www/html/
# Enable headers
RUN a2enmod rewrite headers
EXPOSE 80
CMD ["apache2-foreground"]
