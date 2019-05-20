#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

echo " "
echo "LARAVEL QUEUE"
echo " "

cat >> /etc/supervisor/conf.d/laravel-worker.conf << EOF
[program:horizon]
process_name=%(program_name)s
command=php /vagrant/artisan horizon
autostart=true
autorestart=true
user=vagrant
redirect_stderr=true
stdout_logfile=/vagrant/storage/logs/horizon.log
environment=
    HOME=/vagrant,
    FONTAWESOME_TOKEN=33A0AFE9-91DD-4DA1-9862-D8A2F021D74E
EOF

supervisorctl reread
supervisorctl update
supervisorctl start horizon:*
