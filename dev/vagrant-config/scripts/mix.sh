#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "Laravel Mix"
echo "running as user $_user ($_uid)"
echo " "

export FONTAWESOME_TOKEN='REQUEST FROM SOFTWARE TEAM (LWK)'
echo "export FONTAWESOME_TOKEN=$FONTAWESOME_TOKEN" >> ~/.bashrc

# move to the share folder and use yarn to install deps
mkdir ~/hms2/
cp /vagrant/package.json ~/hms2/
cp /vagrant/package-lock.json ~/hms2/
cp /vagrant/.npmrc ~/hms2/
cd ~/hms2/
npm install > /dev/null 2>&1
rm -rf /vagrant/node_modules
cp -R node_modules /vagrant/

# run Laravel Mix once
cd /vagrant
npm run dev