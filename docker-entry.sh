#!/bin/sh

## server config
php-fpm &
nginx -g "daemon off;"

php migration.sql