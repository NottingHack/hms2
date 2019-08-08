#!/usr/bin/env bash

echo " "
echo "LARAVEL"
echo " "

sudo apt-get install -y unzip > /dev/null 2>&1

# sort out Laravel environment

cp /vagrant/dev/vagrant-config/laravel/.env /vagrant/.env

cd /vagrant
composer install --no-progress --no-suggest

# Set up DB
php artisan key:generate
php artisan doctrine:migration:refresh
php artisan migrate
php artisan hms:database:refresh-views
php artisan hms:database:refresh-procedures
php artisan permission:defaults
php artisan db:seed
php artisan passport:install
php artisan ziggy:generate "resources/js/ziggy.js"

# Setup task scheduler cron
line="* * * * * php /vagrant/artisan schedule:run >> /dev/null 2>&1"
(crontab -u vagrant -l 2>/dev/null; echo "$line" ) | crontab -u vagrant -
