#!/bin/sh
echo "Running Migrations..."
php artisan migrate --force

echo "Starting Supervisor..."
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
