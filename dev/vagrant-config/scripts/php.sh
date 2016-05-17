#!/usr/bin/env bash

echo " "
echo "PHP"
echo " "

debconf-set-selections <<< 'libssl1.0.0:amd64 libssl1.0.0/restart-services string ntp'

apt-get install -y php5-mysql haveged php-pear php5-dev php5-fpm