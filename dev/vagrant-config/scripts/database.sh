#!/usr/bin/env bash

echo " "
echo "MARIADB"
echo " "

debconf-set-selections <<< 'mariadb-server mysql-server/root_password password root'
debconf-set-selections <<< 'mariadb-server mysql-server/root_password_again password root'

# phpmyadmin installs apache
apt-get install -y mariadb-server > /dev/null 2>&1

# Need to setup the DB, etc here - set appropriate privledges
mysql -uroot -proot -e "GRANT ALL ON *.* TO 'hms'@'localhost' IDENTIFIED BY 'secret' WITH GRANT OPTION"
mysql -uroot -proot -e "GRANT ALL ON hms_test.* TO 'travis'@'localhost' WITH GRANT OPTION"
mysql -uroot -proot -e "FLUSH PRIVILEGES"
mysql -uroot -proot -e "CREATE DATABASE hms"
mysql -uroot -proot -e "CREATE DATABASE hms_test"