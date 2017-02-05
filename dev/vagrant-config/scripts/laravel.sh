#!/usr/bin/env bash

echo " "
echo "LARAVEL"
echo " "

php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('SHA384', 'composer-setup.php') === trim(file_get_contents('https://composer.github.io/installer.sig'))) { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php --install-dir=/usr/local/bin --filename=composer
php -r "unlink('composer-setup.php');"

cd /vagrant
composer install

# sort out Laravel environment

cp /vagrant/dev/vagrant-config/laravel/.env /vagrant/.env

# Set up DB
php artisan doctrine:migration:refresh
php artisan db:seed

# Setup task scheduler cron
line="* * * * * php /vagrant/artisan schedule:run >> /dev/null 2>&1"
(crontab -u vagrant -l 2>/dev/null; echo "$line" ) | crontab -u vagrant -
