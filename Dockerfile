# Usa imagen base de PHP con Apache
FROM php:8.2-apache

# Copia los archivos del proyecto al contenedor
COPY . /var/www/html/

# Instala extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Cambia el puerto de Apache al asignado por Render
ENV PORT=10000
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

# Inicia Apache
CMD ["apache2-foreground"]
