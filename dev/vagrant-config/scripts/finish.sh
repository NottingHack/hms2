#!/usr/bin/env bash

echo " "
echo "RESTART SERVICES"
echo " "

systemctl restart nginx.service php7.2-fpm.service php7.4-fpm.service

echo ""
echo "------------------------------------------------------------------------"
echo "         **** HMS should now be running at http://hmsdev/ ****          "
echo ""
echo "Your hosts file needs to have a 'hmsdev' entry."
echo "Linux / MacOS: /etc/hosts"
echo "Windows: C:\Windows\System32\Drivers\etc\hosts"
echo ""
echo "192.168.25.35	hmsdev vimbadmin-api.hmsdev"
echo ""
echo "Info: https://github.com/NottingHack/hms2/blob/master/README.md"
echo ""
echo "phpMyAdmin: https://hsmdev/phpmyadmin/"
echo "mialhog: http://hmsdev:8025/"
echo "vimbadmin-api: http://vimbadmin-api.hmsdev/"
echo ""
echo "MySQL:  username = root,        password = root"
echo "kadmin: username = vagrant      password = vagrant"
echo "HMS:    username = admin        password = admin"
echo "------------------------------------------------------------------------"
echo ""