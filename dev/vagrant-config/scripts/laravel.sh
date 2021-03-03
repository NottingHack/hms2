#!/usr/bin/env bash

echo " "
echo "LARAVEL"
echo " "

# sort out Laravel environment

if [ ! -e /vagrant/.env ]; then
    cp /vagrant/.env.example /vagrant/.env
fi

cd /vagrant

# check we have the submodule
git submodule update --init

composer install --no-progress

# Set up DB
php artisan key:generate
php artisan migrate
php artisan doctrine:migration:refresh
php artisan hms:database:refresh-views
php artisan hms:database:refresh-procedures
php artisan permission:defaults
php artisan db:seed
php artisan passport:install
php artisan ziggy:generate "/vagrant/resources/js/ziggy.js"

# Setup task scheduler cron
line="* * * * * php /vagrant/artisan schedule:run >> /dev/null 2>&1"
(crontab -u vagrant -l 2>/dev/null; echo "$line" ) | crontab -u vagrant -
