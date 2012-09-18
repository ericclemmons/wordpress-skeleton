WordPress Skeleton
==================

Opinionated WordPress starter template that sets up local development
& remote deployment via a simple configuration & command-line tools.


Features
--------

* Simple installation & configuration via [Composer][2] & an interactive `console`
* Local development environment via [Vagrant][1]
* Simple local URLs like `http://local.mysite.com/` via [Vagrant Hostmaster][6]
* Local & remote deployment via [Capistrano][4]
* Simplified theme development via [_s][3] and the WordPress [Theme Unit Test][8]


Dependencies
------------

* Download & Install [Vagrant][1]


Starting a New Theme
--------------------

    > cd path/to/sites
    > git://github.com/ericclemmons/wordpress-skeleton.git
    > cd wordpress-skeleton


Using an Existing Theme
-----------------------

    > cd path/to/existing/site
    > mv path/to/wp-content/themes/mytheme src
    > git remote add skeleton git://github.com/ericclemmons/wordpress-skeleton.git
    > git fetch skeleton && git merge --squash skeleton/master

This will give you the chance to resolve any conflicts in `.gitignore` or `README.md`.

Finally, you can commit your changes with something like:

    > git commit -m 'Migrated theme to WordPress Skeleton'


Setting Up Your Skeleton
------------------------

    > ./bin/install
    > ./bin/console skeleton:configure


Local Development
-----------------

    > vagrant up

The first time you run this, you have to perform a `cold` deployment to setup
the folder structure & database:

    > cap local deploy:cold

After doing it once, you can just do normal deployments, which will only update the theme:

    > cap local deploy


Open WordPress in your browser:

    > ./bin/console skeleton:browse


Now you can make changes to `/src` and refresh!


Useful Commands
---------------

- `./bin/install` is the one-time installation script for setting up dependencies
  needed for the skeleton to operate.

- `./bin/console` will list all possible commands you can run to affect your local
  skeleton's configuration.  These commands are also ran remotely via `cap` to
  perform tasks on the server.

- `cap -T` will list all deployment & WordPress-related commands that can be used
  with each environment.  (You will use `local` the most)


[1]: http://downloads.vagrantup.com/
[2]: http://getcomposer.org/
[3]: http://underscores.me/
[4]: http://capistranorb.com/
[5]: http://github.com/WordPress/WordPress
[6]: http://github.com/mosaicxm/vagrant-hostmaster
[8]: http://codex.wordpress.org/Theme_Unit_Test
