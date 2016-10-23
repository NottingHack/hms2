#!/usr/bin/env bash

echo " "
echo "NGINX"
echo " "

apt-get install -y nginx-full > /dev/null 2>&1

cp /vagrant/dev/vagrant-config/nginx/default /etc/nginx/sites-available/default

