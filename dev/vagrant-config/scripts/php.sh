#!/usr/bin/env bash

echo " "
echo "PHP"
echo " "

debconf-set-selections <<< 'libssl1.0.0:amd64 libssl1.0.0/restart-services string ntp'

# removing php-pear php-dev as they rely on PHP5
apt-get install -y haveged php7.0-fpm php7.0-mysql php7.0-apcu php7.0-json php7.0-curl php-mbstring php7.0-xml php7.0-zip > /dev/null 2>&1

# set php-fpm to run as "vagrant" user
sed -i 's/user = www-data/user = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf