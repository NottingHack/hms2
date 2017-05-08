#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "Laravel Mix"
echo "running as user $_user ($_uid)"
echo " "

# create mix watch script
cat <<\EOF > /home/vagrant/mixwatch.sh
#!/bin/bash
cd /vagrant/
echo "Starting mix watch" >> /vagrant/dev/mixwatch.log;
while [ 1 ]; do
yarn run watch-poll >> /vagrant/dev/mixwatch.log 2>&1;
echo "Restarting mix watch" >> /vagrant/dev/mixwatch.log;
done
EOF

cat <<\EOF > /home/vagrant/restartwatch.sh
#!/bin/bash
pkill -9 -f yarn
/home/vagrant/mixwatch.sh &
EOF

chmod +x /home/vagrant/mixwatch.sh
chmod +x /home/vagrant/restartwatch.sh

# move to the share folder and use yarn to install deps
mkdir ~/hms2/
cp /vagrant/package.json ~/hms2/
cp /vagrant/yarn.lock ~/hms2/
cd ~/hms2/
yarn
rm -rf /vagrant/node_modules
cp -R node_modules /vagrant/

# run Laravel Mix once
cd /vagrant
yarn run dev