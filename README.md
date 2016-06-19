APPLICATION_ENV="development" php -S localhost:8000 -t public/

TODO
* photos: remove sidebar uploader (just use simple upload for now), add photos to some articles
* homepage, /articles, simple search
* admin: homepage
* /tags, tag page
* tags: admin: delete, paginate, create,
* change all dependancies back to 'i18n', 'auth.auth', 'auth.model.user'
* cache busting (foil?)
* auth : remember me
* home - contact form, portfolio
* docs: installation of modules (module/Name/README): add to config, add to composer.json, add to phpunit.xml

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
* testing: library tests, module tests, test 40x when not logged in
* cache: homepage, tags
* photo manage page (delete photos)
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
