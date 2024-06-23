#!/bin/sh

# Source the environment variables
. /etc/environment

APP_KEY=$(cat /run/secrets/app_key)

# Get the PHP script to run from the first argument
SCRIPT_TO_RUN=$1

LOG_FILE="/var/log/$(basename "$SCRIPT_TO_RUN" .php).log"

/usr/local/bin/php /var/www/html/$SCRIPT_TO_RUN $APP_KEY >> $LOG_FILE 2>&1; echo "" >> $LOG_FILE