#!/usr/bin/env bash

cd /vagrant/

mysql -uhmsdev -phmsdev mailserver -e "DELETE FROM alias"
mysql -uhmsdev -phmsdev mailserver -e "DELETE FROM mailbox"

php artisan migrate:reset
php artisan doctrine:migration:refresh
php artisan migrate
php artisan hms:database:refresh-views
php artisan hms:database:refresh-procedures
php artisan permission:defaults
php artisan db:seed
php artisan passport:install