#!/bin/sh

echo "SSL_ENABLED = $SSL_ENABLED"

if [ $SSL_ENABLED = true ]; then
    # Enable Apache SSL and rewrite modules
    a2enmod ssl
    a2enmod rewrite

    mkdir -p /etc/apache2/ssl

    # Copy SSL certificates
    cp /docker/ssl/*.pem /etc/apache2/ssl/

    # Copy Apache configuration to host the 443 port
    cp /docker/ssl/000-default.conf /etc/apache2/sites-available/000-default.conf
fi