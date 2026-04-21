#!/bin/sh

cd /hms

composer upgrade --no-security-blocking

php artisan make:cache-table || true
php artisan key:generate
php artisan migrate
php artisan doctrine:migration:refresh -n
php artisan hms:database:refresh-views
php artisan hms:database:refresh-procedures
php artisan permissions:defaults
php artisan meta:sync
php artisan db:seed
yes | php artisan passport:install
yes | php artisan ziggy:generate
