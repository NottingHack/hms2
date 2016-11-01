#!/usr/bin/env bash

echo " "
echo "RESTART SERVICES"
echo " "

service nginx restart
service php7.0-fpm restart

echo ""
echo "------------------------------------------------------------------------"
echo "         **** HMS should now be running at http://hmsdev/ ****          "
echo ""
echo "Your hosts file needs to have a 'hmsdev' entry."
echo "Linux / MacOS: /etc/hosts"
echo "Windows: C:\Windows\System32\Drivers\etc\hosts"
echo ""
echo "192.168.25.35	hmsdev"
echo ""
echo "Info: https://github.com/NottingHack/hms2/blob/master/README.md"
echo ""
echo "phpMyAdmin: https://hsmdev/phpmyadmin/"
echo "MySQL:  username = root,        password = root"
echo "kadmin: username = vagrant      password = vagrant"
#echo ""
#echo "You can access the database at http://hmsdev/phpmyadmin/"
echo "------------------------------------------------------------------------"
echo ""