<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Blog;

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
                'template_path' => APPLICATION_PATH . '/modules/Blog/views',
            ],

            'photos_dir' => [
                'original' => APPLICATION_PATH . '/data/photos',
                'cache' => APPLICATION_PATH . '/data/photos/cache',
                'public' => '/photos',
            ],

            'mongo' => [
                'classmap' => [
                    'articles' => '\\Blog\\Model\\Article',
                    'photos' => '\\Blog\\Model\\Photo',
                    'tags' => '\\Blog\\Model\\Tag',
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
    //     $classLoader->setPsr4("Blog\\", __DIR__ . "/src");
    // }

    /**
     * Set class maps for class loader to autoload classes for this module
     * @param Container $container
     * @return void
     */
    public function initDependencies(Container $container)
    {
        $settings = self::getModuleConfig();

        $container['blog.file_system'] = function ($c) {
            return new \Blog\FileSystem();
        };

        $container['blog.image'] = function ($c) {
            return new \Blog\Image();
        };

        $container['blog.photo_manager'] = function ($c) {
            return new \Blog\PhotoManager($c['blog.image'], $c['blog.file_system']);
        };

        // models
        $container['blog.model.article'] = function ($c) {
            return new \Blog\Model\Article();
        };
        $container['blog.model.tag'] = function ($c) {
            return new \Blog\Model\Tag();
        };
        $container['blog.model.photo'] = function ($c) {
            return new \Blog\Model\Photo();
        };

        // add folder to $engine
        $templatePath = $settings["renderer"]["template_path"];
        $container['renderer']->addFolder($templatePath);

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

        // create resource method for Slim::resource($route, $name)
        $app->group('/articles', function () use ($container) {

            $controller = new \Blog\Controller\ArticlesController($container);

            $this->get('', $controller('index'))->setName('articles');
            $this->get('/{id:[0-9]+}', $controller('show'))->setName('articles_show');
            $this->get('/{id:[0-9]+}/{slug}', $controller('show'))->setName('articles_show_wslug');
        });

        // photos
        $app->group('/photos', function () use ($container) {
            $controller = new \Blog\Controller\PhotosController($container);
            $this->get('/{path:[0-9]+\/[0-9]+\/[0-9]+\/.+}.jpg', $controller('cached'))->setName('photos_cached');
        });

        // admin routes -- invokes auth middleware
        $app->group('/admin', function () use ($container) {

            // admin/articles routes
            $this->group('/articles', function () use ($container) {

                $controller = new \Blog\Controller\Admin\ArticlesController($container);
                $this->get('', $controller('index'))->setName('admin_articles');
                $this->get('/{id:[0-9]+}', $controller('show'))->setName('admin_articles_show');
                // $this->get('/create', $controller('create'))->setName('admin_articles_create');
                $this->get('/{id:[0-9]+}/edit', $controller('edit'))->setName('admin_articles_edit');
                $this->post('', $controller('post'))->setName('admin_articles_post');
                $this->delete('/{id:[0-9]+}', $controller('delete'))->setName('admin_articles_delete');
                // // these routes must be POST as they contain files and slim doesn't reconize the
                // // _METHOD in multipart/form-data :(
                $this->put('/{id:[0-9]+}', $controller('update'))->setName('admin_articles_update');
                // $this->put('/{id:[0-9]+}/submit', $controller('submit'))->setName('admin_articles_submit');
                // $this->put('/{id:[0-9]+}/approve', $controller('approve'))->setName('admin_articles_approve');

                $controller = new \Blog\Controller\Admin\FilesController($container);
                $this->post('/upload', $controller('upload'))->setName('admin_articles_upload');
            });

            // admin/tags/* routes
            $this->group('/tags', function () use ($container) {
                $controller = new \Blog\Controller\Admin\TagsController($container);
                $this->get('', $controller('index'))->setName('admin_tags');
                // $this->get('/{id:[0-9]+}', $controller('show'))->setName('admin_tags_show');
                $this->get('/create', $controller('create'))->setName('admin_tags_create');
                $this->get('/{id:[0-9]+}/edit', $controller('edit'))->setName('admin_tags_edit');
                $this->post('', $controller('post'))->setName('admin_tags_post');
                $this->put('/{id:[0-9]+}', $controller('update'))->setName('admin_tags_update');
                $this->delete('/{id:[0-9]+}', $controller('delete'))->setName('admin_tags_delete');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

            // admin/articles routes
            $this->group('/data', function () use ($container) {

                $controller = new \Blog\Controller\Admin\DataController($container);

                $this->map(['GET', 'POST'], '/import', $controller('import'))->setName('admin_data_import');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

        })->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
    }
}
