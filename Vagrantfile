# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "debian/contrib-jessie64"
  config.vm.hostname = "hmsdev.nottingtest.org.uk"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/bootstrap.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/nginx.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/database.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/php.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/kerberos.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/node.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/laravel.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/mix.sh", privileged: false
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/labelprinter.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/finish.sh"

  config.vm.network "private_network", ip: "192.168.25.35"

  
end
