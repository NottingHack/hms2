#!/usr/bin/env bash

echo " "
echo "PHP"
echo " "

debconf-set-selections <<< 'libssl1.0.0:amd64 libssl1.0.0/restart-services string ntp'

# removing php-pear php-dev as they rely on PHP5
apt-get install -y haveged php7.0-fpm php7.0-mysql php7.0-apcu php7.0-json php7.0-curl php-mbstring php7.0-xml php7.0-zip php7.0-xdebug > /dev/null 2>&1

# set php-fpm to run as "vagrant" user
sed -i 's/user = www-data/user = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf
sed -i 's/group = www-data/group = vagrant/g' /etc/php/7.0/fpm/pool.d/www.conf
echo 'error_log = /vagrant/storage/logs/php_errors.log' >> /etc/php/7.0/fpm/php.ini
echo 'xdebug.remote_enable = on' >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
echo 'xdebug.remote_connect_back = on' >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini
echo 'xdebug.remote_host = 192.168.25.1' >> /etc/php/7.0/fpm/conf.d/20-xdebug.ini

#phpmyadmin
cd /srv/
wget --progress=bar:force https://files.phpmyadmin.net/phpMyAdmin/4.6.4/phpMyAdmin-4.6.4-english.tar.gz
tar zxf phpMyAdmin-4.6.4-english.tar.gz 
mv phpMyAdmin-4.6.4-english phpmyadmin
chown vagrant:vagrant -R phpmyadmin
cp phpmyadmin/config.sample.inc.php phpmyadmin/config.inc.php