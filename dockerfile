# 1. Imagen base de PHP con Apache
FROM php:8.2-apache

# 2. Instalar extensiones necesarias (mysqli, pdo, etc.)
RUN docker-php-ext-install mysqli pdo pdo_mysql

# 3. Copiar todo el contenido del proyecto
COPY . /var/www/html/

# 4. Establecer el directorio raíz en la carpeta "public"
WORKDIR /var/www/html/public

# 5. Habilitar mod_rewrite (si usas rutas amigables)
RUN a2enmod rewrite

# 6. Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# 7. Exponer el puerto 80
EXPOSE 80

# 8. Iniciar Apache
CMD ["apache2-foreground"]
