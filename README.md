APPLICATION_ENV="development" php -S localhost:8000 -t public/

TODO
* cache: homepage, tags, photos
* photo manage page (delete photos), tag photos for carousel (featured)
* tags: admin: delete, paginate, create, portal: /tags, tag page, public/private (private for e.g. featured)
* cache busting (foil?), remove coffeescript,
* home - contact form, portfolio
* docs: installation of modules (module/Name/README): add to config, add to composer.json, add to phpunit.xml
* delete confirmation on articles: "are you sure...?"
* finishing touches: tidy up registration form, login form;
* sort
* only admin can edit tags 

self hosted
* hosting my website from powburn (lamp, ssl, dns, mail?)
* sync with live db

experiment
* only pass in dependancies rather than container
* inline translation editing (<span data-translation="hello_world">Hello world</span>)
* ajax load with template inheritance (partials/articles_table)
* vendor modules, cp config/views
* elastic search - configurable (mongo, elastic search, )
* ckeditor plugin: select from uploaded media, dropzone

v2
* auth : remember me
* simple search
* admin: homepage - side menu?
* testing: library tests, module tests, test 40x when not logged in

* convert panels like homebox
* comments:
* translations: japanese site
* article preview
* Auth_Facebook module facebook login



further modules:

Articles (admin, tags, photos, etc)
Auth
Auth_Facebook

OauthClient
OauthServer
QA
Store

Translate
- UI for translators
- inline editing
- PUT /translations/hello_world
-




Sites to make:

Martyn.biz
Bisetto.net
Japanese guide to Scotland :)
Shop local
