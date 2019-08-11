#!/usr/bin/env bash

echo " "
echo "ECHO"
echo " "

cp /vagrant/dev/vagrant-config/laravel/laravel-echo-server.json /vagrant/laravel-echo-server.json

systemctl enable laravel-echo-server
systemctl start laravel-echo-server