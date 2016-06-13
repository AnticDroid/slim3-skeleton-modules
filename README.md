APPLICATION_ENV="development" php -S localhost:8000 -t public/

TODO

* articles: admin: homepage, photo manage page (delete photos), preview, styles,
* Tags: delete, paginate, create,
* translations 
* auth : remember me, facebook login, use aura/session?
* photos: dropzone, drag from div to ckeditor,
* cache: homepage, tags?
* admin/users
* use RoleAccess middleware
* home - contact form, portfolio
* library tests, module tests
* router passed to templates, use route paths

* namespace dependencies e.g. "model.user" -> "Auth\Model\User"

* cache busting (plates)
* vendor modules, cp config/views


further modules:

articles (admin, tags, photos, etc)
auth
auth_facebook

oauth_client
oauth_server
qa
store

examples:

martynbiz
* application
* auth
* articles

qa
* application
* auth
* qa

mylocalmap
* application
* auth
* articles
