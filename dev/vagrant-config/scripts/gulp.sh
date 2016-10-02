#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "GULP"
echo "running as user $_user ($_uid)"
echo " "

cd /srv/vagrant
npm install

cd /vagrant/dev/vagrant-config/laravel
./dogulp