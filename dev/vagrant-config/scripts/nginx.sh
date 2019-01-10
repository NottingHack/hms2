#!/usr/bin/env bash

echo " "
echo "NGINX"
echo " "

# now we have the share setup we can enable the site
ln -s /etc/nginx//sites-available/default /etc/nginx/sites-enabled/default

service php7.2-fpm restart
service nginx restart