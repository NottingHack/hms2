#!/usr/bin/env bash

echo " "
echo "ECHO"
echo " "

if [ ! -e /vagrant/laravel-echo-server.json ]; then
    cp /vagrant/dev/vagrant-config/laravel/laravel-echo-server.json /vagrant/laravel-echo-server.json
fi

systemctl enable laravel-echo-server
systemctl start laravel-echo-server