#!/usr/bin/env bash

echo " "
echo "NODE"
echo " "

# add node
curl -sL https://deb.nodesource.com/setup_6.x | sudo -E bash -
# add yarn
curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | sudo apt-key add -
echo "deb https://dl.yarnpkg.com/debian/ stable main" | sudo tee /etc/apt/sources.list.d/yarn.list

apt-get update > /dev/null 2>&1
apt-get install -y nodejs yarn > /dev/null 2>&1
