#!/usr/bin/env bash

echo " "
echo "LARAVEL"
echo " "

cd /vagrant
composer install --no-progress --no-suggest

# sort out Laravel environment

cp /vagrant/dev/vagrant-config/laravel/.env /vagrant/.env

# Set up DB
php artisan doctrine:migration:refresh
php artisan migrate
php artisan permission:defaults
php artisan db:seed
php artisan passport:install


# Setup task scheduler cron
line="* * * * * php /vagrant/artisan schedule:run >> /dev/null 2>&1"
(crontab -u vagrant -l 2>/dev/null; echo "$line" ) | crontab -u vagrant -
