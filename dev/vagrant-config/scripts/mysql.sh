#!/usr/bin/env bash

echo " "
echo "MYSQL"
echo " "

debconf-set-selections <<< 'mysql-server mysql-server/root_password password root'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password root'

apt-get install -y mysql-server > /dev/null 2>&1

# Need to setup the DB, etc here - set appropriate privledges
mysql -uroot -proot -e "GRANT ALL ON *.* TO 'hms'@'localhost' IDENTIFIED BY 'secret' WITH GRANT OPTION"
mysql -uroot -proot -e "GRANT ALL ON hms_test.* TO 'travis'@'localhost' WITH GRANT OPTION"
mysql -uroot -proot -e "FLUSH PRIVILEGES"
mysql -uroot -proot -e "CREATE DATABASE hms"
mysql -uroot -proot -e "CREATE DATABASE hms_test"