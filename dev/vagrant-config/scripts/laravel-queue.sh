#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

echo " "
echo "LARAVEL QUEUE"
echo " "

cat >> /etc/systemd/system/horizon.service << EOF
[Unit]
Description=Laravel Horizon Queue Manager
After=network.target auditd.service mysql.service redis.service
Requires=mysql.service redis.service

[Service]
User=vagrant
Group=vagrant
Environment="HOME=/vagrant"
Environment="FONTAWESOME_TOKEN=33A0AFE9-91DD-4DA1-9862-D8A2F021D74E"
ExecStart=/usr/bin/php /vagrant/artisan horizon
Restart=always

[Install]
WantedBy=multi-user.target
EOF

systemctl daemon-reload
systemctl enable horizon
systemctl start horizon