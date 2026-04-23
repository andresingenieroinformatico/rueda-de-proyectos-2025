FROM php:8.2-apache

# Instala extensiones PHP necesarias (incluyendo pdo_pgsql para Supabase)
RUN apt-get update \
    && apt-get install -y --no-install-recommends libcurl4-openssl-dev libpq-dev \
    && docker-php-ext-install curl mysqli pdo pdo_mysql pdo_pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Habilita mod_rewrite para poder usar .htaccess
RUN a2enmod rewrite

# Asegurar que solo se cargue un MPM (evita "More than one MPM loaded").
# Para mod_php es seguro usar `mpm_prefork`. Deshabilitamos otros MPMs si existen.
RUN a2dismod mpm_event mpm_worker || true && a2enmod mpm_prefork || true

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
