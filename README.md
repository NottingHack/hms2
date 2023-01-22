# hms2
Version 2 of our Hackspace Management System

[![Build Status](https://travis-ci.org/NottingHack/hms2.svg?branch=master)](https://travis-ci.org/NottingHack/hms2)
[![StyleCI](https://github.styleci.io/repos/58862112/shield?branch=main)](https://github.styleci.io/repos/58862112?branch=main)

## Production
### Setup
https://github.com/NottingHack/hms2/wiki/Installation
### Deployment
https://github.com/NottingHack/hms2/wiki/Deployment

## Development
### Cloning
We use submodules, either clone them with this repo
`git clone --recurse-submodules git@github.com:NottingHack/hms2.git`
or after
`git submodule update --init`

### Hosts File

Most of the development environment is taken care of by vagrant, you just need to `vagrant up` to load it.

You do however need to make changes to your hosts file on your machine. Add the following line to your file:

`192.168.25.35	hmsdev`

You can find your file in the following location:

* Windows: C:\Windows\System32\Drivers\etc\hosts
* Mac: /private/etc/hosts
* Linux: /etc/hosts

### Running commands on the Virtual Machine

#### Artisan

To run an artisan command on the VM, (for example if you don't have PHP installed on your local machine) use the ```php-artisan``` script that you can find in the dev/ folder.

Simply pass it the artisan command you want to run, e.g:

```./dev/php-artisan make:controller```

### Vagrant re-provisioning notes

When re-provisioning the box (`vagrnat up`) you must make sure that the `.env` file has `QUEUE_CONNECTION=sync` in order the to the `db:seed` to correctly populate the `mailserver` database entries.

If the `mailserver.mailbox` tables it empty you can do a datebase reset and reseed with `dev/reseed.sh`

### Vagrant suspend notes

When bringing up vagrant box after a suspend or halt some of the service my not start correctly due to the shared folder, run this script to restart them

`dev/restartservices`

### Serving 
ngrok http https://hmsdev:443
