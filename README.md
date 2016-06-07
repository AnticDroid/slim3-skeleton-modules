APPLICATION_ENV="development" php -S localhost:8000 -t public/

TODO

* articles: admin: photos, tags, homepage
* cache: homepage, tags?
* admin/users
* new RoleAccess( [
    Auth\Model\User::ROLE_EDITOR,
    Auth\Model\User::ROLE_ADMIN
] )
* home - contact form, portfolio
* module tests
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
