FROM php:8.2-apache

# Instala extensiones PHP necesarias
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Habilita mod_rewrite para poder usar .htaccess
RUN a2enmod rewrite

# Copia todos los archivos al directorio por defecto
COPY . /var/www/html/

# Cambia DocumentRoot a la carpeta public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf

# Permite que Apache respete el .htaccess para el directorio public
RUN sed -i '/<VirtualHost \*:80>/a \
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
    </Directory>' /etc/apache2/sites-available/000-default.conf

# Copia el entrypoint y lo hace ejecutable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
