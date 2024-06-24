FROM php:8.3-apache

COPY ./docker/ssl/configure_ssl.sh /usr/local/bin/configure_ssl.sh
RUN chmod +x /usr/local/bin/configure_ssl.sh

COPY ./docker/ssl /docker/ssl

ARG SSL_ENABLED

RUN /usr/local/bin/configure_ssl.sh

EXPOSE 80
EXPOSE 443

RUN apt-get update && apt-get install -y \
    libpng-dev \
    cron \
    && docker-php-ext-install mysqli gd

# Uncomment the next line when deploying to production
# RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# This is to prevent removing config.php from gitignore for now. Might be removed when non-docker install gets improved.
COPY ./config.docker.php ./config.php
COPY . /var/www/html

COPY ./docker/run_cron_job.sh /usr/local/bin/run_cron_job.sh
COPY ./docker/cron_jobs /etc/cron.d/cron_jobs

# Ensure the script and cron job files are executable
RUN chmod +x /usr/local/bin/run_cron_job.sh
RUN chmod 0644 /etc/cron.d/cron_jobs

RUN crontab /etc/cron.d/cron_jobs

RUN chmod +x ./docker/start_cron.sh
CMD ./docker/start_cron.sh
