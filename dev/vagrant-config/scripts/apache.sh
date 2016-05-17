#!/usr/bin/env bash

echo " "
echo "APACHE"
echo " "

apt-get install -y apache2

# Enable mod rewrite
ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/
#cp /vagrant/dev/vagrant-config/apache/site.conf /etc/apache2/sites-available/000-default.conf