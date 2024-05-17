#!/bin/bash
chmod -R 775 storage bootstrap/cache
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan db:seed --class=AdminSeeder
php artisan cache:clear
php artisan config:cache
composer dump-autoload
echo "Setup completed successfully."
