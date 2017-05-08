#!/usr/bin/env bash

echo " "
echo "BOOTSTRAP"
echo " "

export DEBIAN_FRONTEND=noninteractive

apt-get update > /dev/null 2>&1

apt-get install -y software-properties-common vim git curl apt-transport-https lsb-release ca-certificates> /dev/null 2>&1

# deb.sury.org
wget --progress=bar:force  -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list

# dotDeb
add-apt-repository -y "deb http://packages.dotdeb.org jessie all"
wget --progress=bar:force https://www.dotdeb.org/dotdeb.gpg 
apt-key add dotdeb.gpg

apt-get update > /dev/null 2>&1

if ! [ -L /srv/www ]; then
  rm -rf /srv/www
  ln -fs /vagrant /srv/www
fi