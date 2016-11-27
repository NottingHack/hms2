#!/usr/bin/env bash

echo " "
echo "Kerberos"
echo " "

debconf-set-selections <<< 'krb5-config krb5-config/add_servers_realm string NOTTINGTEST.ORG.UK'
debconf-set-selections <<< 'krb5-config krb5-config/read_conf boolean true'
debconf-set-selections <<< 'krb5-admin-server krb5-admin-server/kadmind boolean true'
debconf-set-selections <<< 'krb5-config krb5-config/kerberos_servers string hmsdev.nottingtest.org.uk'
debconf-set-selections <<< 'krb5-config krb5-config/default_realm string NOTTINGTEST.ORG.UK'
debconf-set-selections <<< 'krb5-kdc krb5-kdc/debconf boolean true'
debconf-set-selections <<< 'krb5-kdc krb5-kdc/purge_data_too boolean false'
debconf-set-selections <<< 'krb5-admin-server krb5-admin-server/newrealm note'
debconf-set-selections <<< 'krb5-config krb5-config/add_servers boolean true'
debconf-set-selections <<< 'krb5-config krb5-config/admin_server string hmsdev.nottingtest.org.uk'


# Install krb, create passord database, and set the master password to "krbMasterPassword"
apt-get install php-pear php7.0-dev libkrb5-dev haveged krb5-{admin-server,kdc} -y > /dev/null 2>&1
kdb5_util create -s -P krbMasterPassword
mkdir /var/log/kerberos
touch /var/log/kerberos/{krb5kdc,kadmin,krb5lib}.log
chmod -R 750  /var/log/kerberos
echo "vagrant/admin@NOTTINGTEST.ORG.UK * " > /etc/krb5kdc/kadm5.acl
echo "hms/web@NOTTINGTEST.ORG.UK * " >> /etc/krb5kdc/kadm5.acl
/etc/init.d/krb5-kdc start 
/etc/init.d/krb5-admin-server start 

# create some accounts (vagrant, vagrant/admin, admin/admin)
kadmin.local -q "addprinc -pw admin admin"
kadmin.local -q "addprinc -pw vagrant vagrant/admin"
kadmin.local -q "addprinc -pw vagrant vagrant"
kadmin.local -q "addprinc -randkey hms/web"

rm /vagrant/storage/app/hms.keytab
kadmin.local -q "ktadd -k /vagrant/storage/app/hms.keytab hms/web"
chmod a+r /vagrant/storage/app/hms.keytab


# pecl install krb5 - this (still 2016) doesn't have kadm support.
mkdir /root/php-krb
cd /root/php-krb
wget --progress=bar:force http://pecl.php.net/get/krb5-1.1.0.tgz
tar zxf krb5-1.1.0.tgz
cd /root/php-krb/krb5-1.1.0
phpize
./configure --with-krb5kadm=S
make install
ldconfig

echo "extension=krb5.so" >> /etc/php/7.0/mods-available/krb5.ini
ln -s /etc/php/7.0/mods-available/krb5.ini /etc/php/7.0/fpm/conf.d/20-krb5.ini 
ln -s /etc/php/7.0/mods-available/krb5.ini /etc/php/7.0/cli/conf.d/20-krb5.ini 
