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


Installation
------------

**Creating a New Theme**:

    $ cd path/to/sites
    $ git clone git://github.com/ericclemmons/wordpress-skeleton.git my-theme

**Adding to an Existing Theme**:

    $ cd path/to/existing/theme
    $ git remote add skeleton git://github.com/ericclemmons/wordpress-skeleton.git
    $ git fetch skeleton && git merge --squash skeleton/master


* Download & Install [Vagrant][1]
* Install [Vagrant Hostmaster][6]: `$ sudo gem install vagrant-hostmaster`
* Capistrano: `$ sudo gem install capistrano capistrano-ext colored`
* [Composer][2]: `$ curl -s https://getcomposer.org/installer | php`
* Composer depependencies: `$ php composer.phar install`


Configure your `skeleton.yml`
-----------------------------

This is where all WordPress plugins, admin logins & server information is stored.

    $ ./bin/skeleton configure


(Re)Generating Your WordPress Skeleton
--------------------------------------

This is done automatically whenever you configure your `skeleton.yml`, but should
be ran if you make any changes to it manually:

    $ ./bin/skeleton generate


Local Development
-----------------

    $ vagrant up

The first time you run this, you have to perform a `cold` deployment to setup
the folder structure & database:

    $ cap local deploy:cold

After doing it once, you can just do normal deployments, which will only update the theme:

    $ cap local deploy

Open WordPress in your browser:

    $ ./skeleton open

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
