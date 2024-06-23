FROM php:8.3-apache
RUN apt-get update && apt-get install -y \
    libpng-dev \
    && docker-php-ext-install mysqli gd

# Uncomment the next line when deploying to production
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# This is to prevent removing config.php from gitignore for now. Might be removed when non-docker install gets improved.
COPY ./config.docker.php ./config.php
COPY . /var/www/html
USER www-data
