#!/usr/bin/env bash

echo " "
echo "RESTART SERVICES"
echo " "

service nginx restart

echo ""
echo "------------------------------------------------------------------------"
echo "         **** HMS should now be running at http://hmsdev/ ****          "
echo ""
echo "If you cannot access, make sure your hosts file is correct"
echo "See https://github.com/NottingHack/hms2/blob/master/README.md"
echo ""
echo "MySQL:  username = root,        password = root"
echo ""
echo "You can access the database at http://hmsdev/phpmyadmin/"
echo "------------------------------------------------------------------------"
echo ""