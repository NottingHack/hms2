#!/bin/sh

HOME=/tmp/hms
mkdir $HOME

cd /hms

composer upgrade --no-security-blocking

[ -f .env ] || (
    cp .env.example .env
    sed -i '/MAIL_HOST/s/hmsdev/hms-mailpit/' .env
    php artisan key:generate
)

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
