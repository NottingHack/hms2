Behat Testing
=============

Acceptance/functional testing of HMS features is carried out using behat. [Behat](http://docs.behat.org/en/v3.0/) is an implementation of the Gerkin test language for PHP.

The context files as implemented run a migration reset and refresh before **every** scenario. To this end it also uses a .env.behat file to configure the system to use purely test config. _**DO NOT**_ point it at your development database if you wish to retain data when running tests.

This does mean that your code will need the relevant factories created so that the database can be seeded to a level to run your tests.

To run the tests you need to be inside the vagrant box:

`$ cd ${PROJECT_DIR}`
`vagrant ssh`
`cd /vagrant'
`$ vendor/bin/behat`
