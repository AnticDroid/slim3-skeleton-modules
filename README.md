# Metroworks Auth #

This is the authentication app currently used by JapanTravel and related applications such as
Q&A. It is written in Slim Framework v3 with some additional libraries to offer controller and
extended view functionality.

It has unit and integrated tests written for anywhere that would be deemed testable. Please run
these tests before and after development and ensure they all pass. Feel free to contribute to tests
whilst developing new sections or bug fixing.

## MWAuth installation ##

This is to install the MWAuth authentication app.

### Files ###

The following commands will bring all the required files to your local machine (e.g. /var/www/):

```
$ mkdir jt_sso && cd jt_sso
$ git clone git@gitlab.metroworks.co.jp:sso/mwauth.git website
$ cd website
$ composer install
```

Note: Might want to set permissions of jt_sso directory in accordance with your local environment
Note: composer is outside the main tree to allow us to push-deploy with rollback directories.

### Local database ###

Create local MySQL database:

```mysql
mysql> CREATE DATABASE sso_development;
mysql> CREATE DATABASE sso_testing;
```

Run migrations

```
$ ../vendor/bin/phinx init # then edit the generated phinx.yml file development and testing sections
$ ../vendor/bin/phinx migrate -e development
$ ../vendor/bin/phinx migrate -e testing
```

Note: this will create empty databases. For live data, see section below after installation.

### Virtual host ###

Note: the domain that SSO authentication app is installed at must match the cookie domain of other
sites such as JapanTravel (e.g. .jt.martyndev or .jt.guilhemdev). Set the domain in your vhost config
and app/config/development.php

/etc/apache/sites/available/jt_sso.conf

```
<VirtualHost *:80>
    ServerName      sso.jt.martyndev
    ServerAlias     sso.jt.martyndev

    php_value session.save_handler "redis"
    php_value session.save_path "tcp://127.0.0.1:6379"

    DocumentRoot /var/www/jt_sso/website/public
    <Directory /var/www/jt_sso/website/public>
        Options FollowSymLinks
        AllowOverride All
    </Directory>

    SetEnv APPLICATION_ENV "development"

    # Logging
    ErrorLog /var/log/apache2/jt.error.log
    CustomLog /var/log/apache2/jt.access.log combined
</VirtualHost>
```

Note: redis configuration must match ACQ vhost configuration so that session data is shared

/etc/hosts

```
172.28.128.3       sso.japantravel.vagrant
```

Add site to apache enabled-sites (a2ensite) and reload apache. Site should be running at
sso.jt.martyndev or whatever you set it to.

### Config ###

Config works by picking up the global config file, and merging any environment files
that may exist (e.g. production.php). If no environment file exists then nothing is
merged.

Copy the `production.php` file for the environment(s) of the current installation and
make necessary changes:

```
cd app/config
cp production.php development.php
cp production.php testing.php
```

These copied files will not make it into version control, and your development host environment
should match the development* filename. As no one else will be sharing these files, "development"
is fine (unlike ACQ which requires something like "development_<name>"). Although, you can put
whatever you please.

If working in development, set 'displayErrorDetails' => true, as this will help debug installation errors
Also, set database credentials and update the URLs.

### Setup directories, permissions, etc ###

```
./scripts/install.sh
```

## Sync live data ##

```
$ echo -e "MYSQL_USER=myuser\nMYSQL_PASS=mypass\nMYSQL_DB=sso_development" > scripts/sync.conf
$ ./scripts/sync.sh
```

## Troubleshooting ##

'''Can't see SSO session variables being shared across applications?'''

Ensure PHP INI session.* are the same across all applications including SSO. Checked session.cookie_domain, session.save_handler, session.save_path etc. In JT we defined some of these in the apache config so might wanna check there. Cookie domain needs to be something like .japantravel.com.

Also, ensure that another session storage isn't being used (e.g. redis). The authentication app must be using the same
storage.

```Blade templates cannot write to a file```

This may be bacause the file has a different owner from www-data. This can result from running tests which appear to
create the cached file as the owner who runs the tests. Amend your app/config/testing.php file like so:

```
'renderer' => [
    'cache_path' => APPLICATION_PATH . '/../data/trash',
],
```

Anything within /data is not added to version control.

## Controllers ##

Controllers use Slim3Controller:

https://github.com/martynbiz/slim3-controller

## Views ##

Laravel's Blade templates have been used for views

https://laravel.com/docs/5.1/blade

### Translations ###

Zend i18n - https://zendframework.github.io/zend-i18n/

There are a couple of helper methods just so we don't have to write our long object->method calls:

translate('missing_username'); // calls zend-i18n::translate()
plural('');  // calls zend-i18n::translatePlural()

Currently translations are stored in PHP arrays. This is due to the fact that the previous software
used PHP arrays and it was easier to copy them over for now. Might be good to put these into PO/MO
files. Anyway for now, to keep keys up to date, there is a small CLI tool to scan for changes and
generate PHP array files. For example:

```
$ ../vendor/bin/translatetool scan en # dry run for "en" lang
$ ../vendor/bin/translatetool update en # sync "en" file
$ ../vendor/bin/translatetool # will show all commands available (e.g. scan, update)
```

## Assets ##

Assets such as CSS and JS are managed by Gulp. Gulp tasks are defined in /gulpfile.js

To compile css/js, run:

```
$ npm install
```

Once changes have been made to the LESS or JavaScript files, run to compile:

```
$ gulp
```

To compile individually, run:

```
$ gulp css
$ gulp js
```

To watch for changes and not have to worry about manually compiling, run:

```
$ gulp watch
```

## Models ##

Models are powered by Eloquent:

https://laravel.com/docs/5.1/eloquent

TODO config

## Sessions ##

Session variables are set within the MWAuth app and thanks to the cookie_domain setting
are available across multiple sub-domains.

To keep things simple for other apps without having to require additional libraries to access
session variables within wrappers, $_SESSION is accessed directly - rather than session managers
such as zend that put a serialized object in the session (which requires zend session in every
dependant app to recreate the object). This means only an array will be written to the session.

TODO are we using sesson manager object here?

### Remember me ###

Remember me cookie is stored which contains an identifier and a validator (token). When the
user clicks the "remember me" checkbox upon authenticating, this cookie is set to a long term
duration (e.g. 3 months). However, as each app is on different systems from MW Auth where the
accounts database table is, when a user who is not authenticated but has a remember_me cookie
the MW Auth client will do a redirect to set the session for that user. They will be redirected
back to the app - this should all happen passively, without the user being aware. However, some
issues may arise when we have post data - 307 redirect?

## Testing ##

Tests have been split into sub-folders within the tests directory. They can be run together
by running:

```
./vendor/bin/phpunit
```

## Debugging ##

There is a nice little tool that produced well formatted dumps:

```
d($accounts);
```

Kint - https://github.com/raveren/kint/

## Flash messages ##

Flash messages should be used for error messages, not required for success messages as in
most cases the user will be redirect back to the app they came from. Error messages are used
when, for example, the registration form hasn't been filled in properly.

Uses https://github.com/martynbiz/php-flash-message


## TODO




once done, pre-deploy
* search for TODOs
* merge with devel - update pootle, email welcome emails/ forgot password?

* *tidy up code
  - do we need logged_in?
  - rm login.phtml

# DEPLOY #

* git-deploy sso app - test jt users, not facebook yet
* git-deploy qa - test working jt users, not facebook yet

- test qa looks ok, jt/fb logins, register

* git-deploy jt - test working jt users, not facebook yet

# POST-DEPLOY #

* vagrant - doesn't require saml, config mwauth-client to staging


v2
* oauth server



future
* convert php arrays to po/mo files
* declare $app global in tests? then we don't need to instantiate it every time :/
* put meta name validation in the meta model
* use session wrapper?
* mongo vs eloquent - nice change to try my mongo odm, validator for real
* put the $router in the view for $router->pathFor('index_index')
* config - martynbiz/php-config - define path, global/env config files, ArrayAccess
* https://packagist.org/packages/league/oauth2-client
* passive login? -- this is where if there is an error, just return back to app?
* http://gatekeeper-auth.readthedocs.org/en/latest/installation-and-configuration/

http/ https session sharung
https? http://stackoverflow.com/questions/441496/session-lost-when-switching-from-http-to-https-in-php
https://simplesamlphp.org/docs/development/simplesamlphp-nostate#section_3_1_1
solution? http://stackoverflow.com/questions/441496/session-lost-when-switching-from-http-to-https-in-php

v2
* admin
  * graphs home - new signups, weekly logins,
  * manage users, login as admin?


martynbiz/slim3-contoller
* can we run tests with run() instead? then we can use App for bootstrap (eg. routes)
* query
* more appropriate error messages for TestCase's assert* methods
* validate json
* assertViewReceives

martynbiz/php-flash-message - v1.1
* pushMessage('success', '...'); // will push values to an array, if key value is a str, make it an array?
* addMessage(''); // will set message, if key value is an array then do push, if str overwrite
