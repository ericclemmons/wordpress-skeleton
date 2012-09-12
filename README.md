WordPress Skeleton
==================

WordPress skeleton utilizing [Vagrant][1], [Composer][2], [_s][3], & [Wordpress][5].


Dependencies
------------

1. Download & Install [Vagrant][1]


Geting Started
--------------

Install [Vagrant Hostmaster][6], [Composer][2], [WordPress][5] & other dependencies:

    ./bin/install

Configure & generate the skeleton:

    ./bin/console skeleton:configure
    ./bin/console skeleton:generate


Local Development
-----------------

    vagrant up

After deploying, open <http://develop.[domain]/> in your browser,
make changes to your theme in `/src`, & refresh!


Deployment
----------

    cap develop deploy:cold

This will automatically run the following:

    cap develop deploy:setup                # First time setup of remote folder structure
    cap develop deploy                      # Setups up WordPress configs & themes
    cap develop wordpress:db:create         # First time creation of database
    cap develop wordpress:install           # First time initialization of database & admin user
    cap develop wordpress:theme:activate    # Activate your theme

You can specify other environments like `staging` and `production`, if you have those setup.

[1]: http://vagrantup.com/
[2]: http://getcomposer.org/
[3]: http://underscores.me/
[5]: http://github.com/WordPress/WordPress
[6]: http://github.com/mosaicxm/vagrant-hostmaster
[7]: http://getcomposer.org/doc/00-intro.md#globally
[8]: http://codex.wordpress.org/Theme_Unit_Test
