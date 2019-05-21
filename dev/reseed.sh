#!/usr/bin/env bash

cd /vagrant/

mysql -uroot -proot mailserver -e "DELETE FROM alias"
mysql -uroot -proot mailserver -e "DELETE FROM mailbox"

php artisan migrate:reset
php artisan doctrine:migrations:refresh
php artisan migrate
php artisan hms:database:refresh-views
php artisan hms:database:refresh-procedures
php artisan permissions:defaults
php artisan db:seed
php artisan passport:install