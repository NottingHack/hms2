#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "GULP"
echo "running as user $_user ($_uid)"
echo " "

# move to the share folder and use yarn to install deps
mkdir ~/hms2/
cp /vagrant/package.json ~/hms2/
cp /vagrant/yarn.lock ~/hms2/
cd ~/hms2/
yarn
cp -R node_modules /vagrant/

# run Laravel Mix once
cd /vagrant
yarn run dev