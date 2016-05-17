#!/usr/bin/env bash

echo " "
echo "BOOTSTRAP"
echo " "

export DEBIAN_FRONTEND=noninteractive

apt-get update

apt-get install -y software-properties-common

# dotDeb
add-apt-repository -y "deb http://packages.dotdeb.org jessie all"
wget https://www.dotdeb.org/dotdeb.gpg
apt-key add dotdeb.gpg

apt-get update

if ! [ -L /srv/www ]; then
  rm -rf /srv/www
  ln -fs /vagrant /srv/www
fi