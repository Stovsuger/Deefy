FROM php:8.3-apache
RUN docker-php-ext-install pdo pdo_mysql
# main.php est le point d'entrée du site
RUN printf 'DirectoryIndex main.php index.html\n' > /etc/apache2/conf-available/deefy.conf \
 && a2enconf deefy
COPY docker/php.ini /usr/local/etc/php/conf.d/deefy.ini
WORKDIR /var/www/html
