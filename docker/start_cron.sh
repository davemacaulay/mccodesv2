#!/bin/sh

touch /var/log/cron_minute.log /var/log/cron_fivemins.log /var/log/cron_hour.log /var/log/cron_day.log

# Copy the env variables needed for the cron jobs
printenv | grep "DB_HOST\|DB_NAME\|DB_USER\|DB_PASS\|DB_PASS_FILE\|APP_KEY\|APP_KEY_FILE" >> /etc/environment

cron

apache2-foreground
