#!/bin/sh

HOME=/tmp

cd /hms

composer upgrade --no-security-blocking

[ -f .env ] || (
    cp .env.example .env
    php artisan key:generate
)

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

npm install
npm run dev
