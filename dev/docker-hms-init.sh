#!/bin/sh

HOME=/tmp/hms
mkdir -p $HOME

cd /hms

# Stuff to happen on the first run only
[ -f .env ] || (
    cp .env.example .env
    sed -i '/MAIL_HOST/cMAIL_HOST=hms-mailpit/' .env
    sed -i '/REDIS_HOST/cREDIS_HOST=hms-redis/' .env
    sed -i '/MEMCACHED_HOST/cMEMCACHED_HOST=hms-memcached/' .env

    composer install --no-security-blocking

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

    npm ci
    npm run dev
)

