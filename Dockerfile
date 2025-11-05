# Imagen base con PHP y Apache
FROM php:8.2-apache

# Instala extensiones necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copia todo el proyecto al contenedor
COPY . /var/www/html/

# Cambia el directorio raíz de Apache a la carpeta "public"
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

# Configura el puerto que Render usa
ENV PORT=10000
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 10000

# Inicia Apache
CMD ["apache2-foreground"]
