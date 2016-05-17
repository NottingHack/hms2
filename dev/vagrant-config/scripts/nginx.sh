#!/usr/bin/env bash

echo " "
echo "NGINX"
echo " "

apt-get install -y nginx-full

cp /vagrant/dev/vagrant-config/nginx/default /etc/nginx/sites-available/default

