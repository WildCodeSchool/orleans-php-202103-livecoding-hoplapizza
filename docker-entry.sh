#!/bin/sh

## server config
php-fpm &
nginx -g "daemon off;"

mysql -u ${DB_USER} -p ${DB_PASSWORD} -h ${DB_HOST} -d ${DB_NAME} < database.sql