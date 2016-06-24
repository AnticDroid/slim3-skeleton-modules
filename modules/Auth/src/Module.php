<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Auth;

use Composer\Autoload\ClassLoader;
use Slim\App;
use Slim\Container;
use MartynBiz\Slim3Module\AbstractModule;
use MartynBiz\Mongo\Connection;

use Auth\Model\User;

class Module extends AbstractModule
{
    /**
     * Get config array for this module
     * @return array
     */
    public function getModuleConfig()
    {
        return [
            'renderer' => [
                'template_path' => APPLICATION_PATH . '/modules/Auth/views',
            ],

            'auth' => [

                // this is the session namespace. apps that want to authenticate
                // using this auth app must configure their mwauth-client to match
                'namespace' => 'slim3__auth__',

                // remember me cookie settings
                'auth_token' => [
                    'cookie_name' => 'auth_token',
                    'expire' => strtotime("+3 months", time()), // seconds from now
                    'path' => '/',
                ],

                // remember me cookie settings
                'recovery_token' => [
                    'expire' => strtotime("+1 hour", time()), // seconds from now
                ],

                // these are attributes that will be written to session
                'valid_attributes' => [
                    'id',
                    'first_name',
                    'last_name',
                    'name',
                    'email',
                    'facebook_id',
                ],
            ],

            'mongo' => [
                'classmap' => [
                    'users' => '\\Auth\\Model\\User',
                ],
            ],
        ];
    }

    // /**
    //  * Set class maps for class loader to autoload classes for this module
    //  * @param ClassLoader $classLoader
    //  * @return void
    //  */
    // public function initClassLoader(ClassLoader $classLoader)
    // {
    //     $classLoader->setPsr4("Auth\\", __DIR__ . "/src");
    // }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param Container $container
     * @return void
     */
    public function initDependencies(Container $container)
    {
        $settings = self::getModuleConfig();

        // Models
        $container['auth.model.user'] = function ($c) {
            return new \Auth\Model\User();
        };

        $container['auth'] = function ($c) {
            $settings = $c->get('settings')['modules']['Auth']['auth'];
            $authAdapter = new \Auth\Adapter\Mongo( $c['auth.model.user'] );
            return new \Auth\Auth($authAdapter, $settings);
        };

        // add template_path folder to $engine
        $container['renderer']->addFolder($settings["renderer"]["template_path"]);

        // add models to mongo
        Connection::getInstance()->appendClassMap($settings['mongo']['classmap']);
    }

    /**
     * Load is run last, when config, dependencies, etc have been initiated
     * Routes ought to go here
     * @param App $app
     * @return void
     */
    public function initRoutes(App $app)
    {
        $container = $app->getContainer();

        $app->group('/auth', function () use ($container) {

            $controller = new \Auth\Controller\SessionController($container);

            $this->post('', $controller('post'))->setName('auth_session_post');
            $this->delete('', $controller('delete'))->setName('auth_session_delete');

            $this->get('/login', $controller('index'))->setName('auth_session_login');
            $this->get('/logout', $controller('index'))->setName('auth_session_logout');

            $this->group('/register', function () use ($container) {

                $controller = new \Auth\Controller\UsersController($container);

                $this->get('', $controller('create'))->setName('auth_users_create');
                $this->post('', $controller('post'))->setName('auth_users_post');

                $this->get('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password');
                $this->post('/resetpassword', $controller('resetpassword'))->setName('auth_users_reset_password_post');
            });
        });

        // admin routes -- invokes auth middleware
        $app->group('/admin', function () use ($container) {

            // admin/users routes
            $this->group('/users', function () use ($container) {

                $controller = new \Auth\Controller\Admin\UsersController($container);

                $this->get('', $controller('index'))->setName('admin_users');
                $this->get('/{id:[0-9]+}', $controller('show'))->setName('admin_users_show');
                $this->get('/create', $controller('create'))->setName('admin_users_create');
                $this->get('/{id:[0-9]+}/edit', $controller('edit'))->setName('admin_users_edit');

                // $this->post('', $controller('post'))->setName('admin_users_post');
                $this->put('/{id:[0-9]+}', $controller('update'))->setName('admin_users_update');
                $this->delete('/{id:[0-9]+}', $controller('delete'))->setName('admin_users_delete');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

        })->add( new \Auth\Middleware\Auth( $container['auth'] ) );
    }
}
