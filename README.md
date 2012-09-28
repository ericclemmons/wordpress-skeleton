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

You will need to install Vagrant and some Ruby Gems for both local development
and deployment to work correctly.

* Download & Install [Vagrant][1]
* Install [Vagrant Hostmaster][6]

  > sudo gem install vagrant-hostmaster

* Capistrano

  > sudo gem install capistrano capistrano-ext colored


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

    > ./skeleton configure


Saving Your Skeleton
--------------------

    > git add Vagrantfile config src
    > git commit -m "Initial Skeleton commit"

- `Vagrantfile` defines your local VM's settings.
- `config/` contains deployment scripts and `skeleton.yml`, which stores your options
  for regenerating your skeleton from scratch, should you need to.
- `src/` houses your theme folder and is linked & activated within WordPress upon
  deployment.

You can view your skeleton's configuration at anytime by viewing `config/skeleton.yml` or running

    > ./skeleton info


Resetting the Skeleton Core
---------------------------

If you ever update the core `./skeleton` or make changes to any of its dependencies
in `/vendor`, it may be wise to reset the skeleton core back to when it was installed:

    > ./skeleton reset


Local Development
-----------------

    > vagrant up

The first time you run this, you have to perform a `cold` deployment to setup
the folder structure & database:

    > cap local deploy:cold

After doing it once, you can just do normal deployments, which will only update the theme:

    > cap local deploy

Open WordPress in your browser:

    > ./skeleton open

Now you can make changes to `/src` and refresh!

You can always view `skeleton.yml` or run `./skeleton info` for WordPress Admin credentials.


Useful Commands
---------------

- `./skeleton` will list all possible commands you can run to affect your local
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
