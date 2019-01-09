#!/usr/bin/env bash

_user="$(id -u -n)"
_uid="$(id -u)"

echo " "
echo "Laravel Mix"
echo "running as user $_user ($_uid)"
echo " "

export FONTAWESOME_TOKEN='33A0AFE9-91DD-4DA1-9862-D8A2F021D74E'
echo "export FONTAWESOME_TOKEN=$FONTAWESOME_TOKEN" >> ~/.bashrc

# move to the share folder and use yarn to install deps
mkdir ~/hms2/
cp /vagrant/package.json ~/hms2/
cp /vagrant/yarn.lock ~/hms2/
cp /vagrant/.npmrc ~/hms2/
cd ~/hms2/
yarn
rm -rf /vagrant/node_modules
cp -R node_modules /vagrant/

# run Laravel Mix once
cd /vagrant
yarn run dev