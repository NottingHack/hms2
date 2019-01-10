#!/usr/bin/env bash

export DEBIAN_FRONTEND=noninteractive

echo " "
echo "LARAVEL QUEUE"
echo " "

cat >> /etc/supervisor/conf.d/laravel-worker.conf << EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /vagrant/artisan doctrine:queue:work redis --daemon --sleep=3 --tries=3
autostart=true
autorestart=true
user=vagrant
numprocs=1
redirect_stderr=true
stdout_logfile=/vagrant/storage/logs/worker.log
EOF

supervisorctl reread
supervisorctl update
supervisorctl start laravel-worker:*