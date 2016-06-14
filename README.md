APPLICATION_ENV="development" php -S localhost:8000 -t public/

TODO

* articles: admin: homepage, photo manage page (delete photos), preview, styles,
* homepage: tags list, tags page
* users: admin, roles, use RoleAccess middleware
* tags: admin: delete, paginate, create,
* quick n easy: pathFor
* translations: japanese site
* comments:
* auth : remember me, facebook login, use aura/session?
* photos: dropzone, drag from div to ckeditor,
* cache: homepage, tags?
* admin/users
* home - contact form, portfolio
* testing: library tests, module tests, test 40x when not logged in
* docs: installation of modules (module/Name/README): add to config, add to composer.json, add to phpunit.xml

* namespace dependencies e.g. "model.user" -> "Auth\Model\User"

* cache busting (plates)
* vendor modules, cp config/views
* sync with live db


further modules:

Articles (admin, tags, photos, etc)
Auth
AuthFacebook

OauthClient
OauthServer
QA
Store -
Translate - UI for translators
