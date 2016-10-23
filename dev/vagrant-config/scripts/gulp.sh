#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "GULP"
echo "running as user $_user ($_uid)"
echo " "

# move to the share folder and use yarn to install deps
cd /vagrant
/usr/bin/yarn

# create gulp watch script
cat <<\EOF > /home/vagrant/gulpwatch.sh
#!/bin/bash
cd /vagrant/
echo "Starting gulp watch" >> /vagrant/dev/gulpwatch.log;
while [ 1 ]; do
gulp watch >> /vagrant/dev/gulpwatch.log 2>&1;
echo "Restarting gulp watch" >> /vagrant/dev/gulpwatch.log;
done
EOF

cat <<\EOF > /home/vagrant/restartwatch.sh
#!/bin/bash
pkill -9 -f gulp
/home/vagrant/gulpwatch.sh &
EOF

chmod +x /home/vagrant/gulpwatch.sh
chmod +x /home/vagrant/restartwatch.sh

# run gulp once
/usr/bin/gulp