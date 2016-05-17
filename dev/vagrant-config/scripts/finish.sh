#!/usr/bin/env bash

echo " "
echo "RESTART SERVICES"
echo " "

service nginx restart

echo ""
echo "------------------------------------------------------------------------"
echo " **** Altruismo should now be running at http://localhost:8080/ **** "
echo ""
echo "MySQL:  username = root,        password = root"
echo ""
echo "You can access the database at http://localhost:8080/phpmyadmin/"
echo "------------------------------------------------------------------------"
echo ""