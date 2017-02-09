#!/usr/bin/env bash

echo " "
echo "NGINX"
echo " "

apt-get install -y nginx-full > /dev/null 2>&1
mkdir /etc/nginx/ssl
openssl genrsa -out /etc/nginx/ssl/hmsdev.key 2048 > /dev/null 2>&1
openssl req -new -x509 -key /etc/nginx/ssl/hmsdev.key -out /etc/nginx/ssl/hmsdev.cert -days 3650 -subj /CN=hmsdev > /dev/null 2>&1

cp /vagrant/dev/vagrant-config/nginx/default /etc/nginx/sites-available/default