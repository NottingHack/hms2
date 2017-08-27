# -*- mode: ruby -*-
# vi: set ft=ruby :

# Vagrantfile API/syntax version. Don't touch unless you know what you're doing!
VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  # All Vagrant configuration is done here. The most common configuration
  # options are documented and commented below. For a complete reference,
  # please see the online documentation at vagrantup.com.

  # Every Vagrant virtual environment requires a box to build off of.
  config.vm.box = "NottingHack/hms2"
  config.vm.box_version = ">=0.4.0"
  config.vm.hostname = "hmsdev.nottingtest.org.uk"
  
  config.vm.provider :virtualbox do |vb|
    vb.customize ['modifyvm', :id, '--memory', '2048']
    vb.customize ['modifyvm', :id, '--natdnsproxy1', 'on']
    vb.customize ['modifyvm', :id, '--natdnshostresolver1', 'on']
  end

  config.vm.provision :shell, path: "dev/vagrant-config/scripts/nginx.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/laravel.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/mix.sh", privileged: false
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/labelprinter.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/laravel-queue.sh"
  config.vm.provision :shell, path: "dev/vagrant-config/scripts/finish.sh"

  config.vm.network "private_network", ip: "192.168.25.35"

  
end
