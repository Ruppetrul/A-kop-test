#!/bin/bash

while ! nc -z db 5432; do
    echo "Waiting for PostgreSQL to be available..."
    sleep 1
done

php artisan migrate --force
php artisan db:seed

exec apache2-foreground
