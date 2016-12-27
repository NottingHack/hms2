#!/usr/bin/env bash

echo " "
echo "BOOTSTRAP"
echo " "

export DEBIAN_FRONTEND=noninteractive

apt-get update > /dev/null 2>&1

apt-get install -y software-properties-common vim git curl > /dev/null 2>&1

# dotDeb
add-apt-repository -y "deb http://packages.dotdeb.org jessie all"
wget --progress=bar:force https://www.dotdeb.org/dotdeb.gpg 
apt-key add dotdeb.gpg

apt-get update > /dev/null 2>&1

if ! [ -L /srv/www ]; then
  rm -rf /srv/www
  ln -fs /vagrant /srv/www
fi