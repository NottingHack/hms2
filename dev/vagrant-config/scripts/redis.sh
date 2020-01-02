#!/usr/bin/env bash

echo " "
echo "redis"
echo " "

# open up redis
sed -i 's/bind 127.0.0.1/bind 127.0.0.1 192.168.25.35/' /etc/redis/redis.conf

systemctl restart redis

