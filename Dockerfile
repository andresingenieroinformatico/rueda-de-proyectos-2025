FROM php:8.2-apache

RUN docker-php-ext-install mysqli pdo pdo_mysql

# Copiar primero la plantilla de configuración
COPY config/config.php /var/www/html/config/config.php

# Copiar el resto de archivos
COPY . /var/www/html/

RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf
## Atento: no fijamos el puerto en build time. Usaremos un entrypoint que adapte la configuración en runtime
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]
