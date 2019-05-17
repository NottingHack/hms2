#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

echo " "
echo "LARAVEL QUEUE"
echo " "

cat >> /etc/supervisor/conf.d/laravel-worker.conf << EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /vagrant/artisan doctrine:queue:work redis --daemon --sleep=3 --tries=3 --queue=default
autostart=true
autorestart=true
user=vagrant
numprocs=1
redirect_stderr=true
stdout_logfile=/vagrant/storage/logs/worker.log

[program:laravel-maintance]
process_name=%(program_name)s_%(process_num)02d
command=php /vagrant/artisan doctrine:queue:work redis --daemon --sleep=3 --tries=3 --queue=maintance --force
autostart=true
autorestart=true
user=vagrant
numprocs=1
redirect_stderr=true
stdout_logfile=/vagrant/storage/logs/worker-maintance.log
environment=
    HOME=/vagrant,
    FONTAWESOME_TOKEN=33A0AFE9-91DD-4DA1-9862-D8A2F021D74E
EOF

supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*
supervisorctl start laravel-maintance:*
