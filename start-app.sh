#!/bin/bash
echo "Waiting 15 seconds for MySQL to be ready..."
sleep 15
echo "Running migrations..."
php artisan migrate --force
echo "Starting application..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
