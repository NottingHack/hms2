#!/usr/bin/env bash

echo " "
echo "MYSQL"
echo " "

debconf-set-selections <<< 'mariadb-server mysql-server/root_password password root'
debconf-set-selections <<< 'mariadb-server mysql-server/root_password_again password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/dbconfig-install boolean true'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/admin-pass password root'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/app-password-confirm password phpmyadmin'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/mysql/app-pass password mysql'
debconf-set-selections <<< 'phpmyadmin phpmyadmin/reconfigure-webserver multiselect apache2'

# phpmyadmin installs apache
apt-get install -y mariadb-server  > /dev/null 2>&1

# Need to setup the DB, etc here - set appropriate privledges
mysql -uroot -proot -e "GRANT ALL ON *.* TO 'altruismo'@'localhost' IDENTIFIED BY '' WITH GRANT OPTION"
mysql -uroot -proot -e "FLUSH PRIVILEGES"