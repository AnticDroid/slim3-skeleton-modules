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

        $app->group('/articles', function () use ($app) {
            $app->get('', '\Blog\Controller\ArticlesController:index')->setName('articles');
            $app->get('/{id:[0-9]+}', '\Blog\Controller\ArticlesController:show')->setName('articles_show');
            $app->get('/{id:[0-9]+}/{slug}', '\Blog\Controller\ArticlesController:show')->setName('articles_show_wslug');
        });

        $app->group('/photos', function () use ($app) {
            $app->get('/{path:[0-9]+\/[0-9]+\/[0-9]+\/.+}.jpg', '\Blog\Controller\PhotosController:cached')->setName('photos_cached');
        });

        $app->group('/admin', function () use ($app, $container) {

            $app->group('/articles', function () use ($app) {

                $app->get('', '\Blog\Controller\Admin\ArticlesController:index')->setName('admin_articles');
                $app->get('/{id:[0-9]+}', '\Blog\Controller\Admin\ArticlesController:show')->setName('admin_articles_show');
                $app->get('/{id:[0-9]+}/edit', '\Blog\Controller\Admin\ArticlesController:edit')->setName('admin_articles_edit');
                $app->post('', '\Blog\Controller\Admin\ArticlesController:post')->setName('admin_articles_post');
                $app->delete('/{id:[0-9]+}', '\Blog\Controller\Admin\ArticlesController:delete')->setName('admin_articles_delete');
                $app->put('/{id:[0-9]+}', '\Blog\Controller\Admin\ArticlesController:update')->setName('admin_articles_update');

                $app->post('/upload', '\Blog\Controller\Admin\FilesController:upload')->setName('admin_articles_upload');
            });

            // admin/tags/* routes
            $app->group('/tags', function () use ($app) {
                $app->get('', '\Blog\Controller\Admin\TagsController:index')->setName('admin_tags');
                // $app->get('/{id:[0-9]+}', '\Blog\Controller\Admin\TagsController:show')->setName('admin_tags_show');
                $app->get('/create', '\Blog\Controller\Admin\TagsController:create')->setName('admin_tags_create');
                $app->get('/{id:[0-9]+}/edit', '\Blog\Controller\Admin\TagsController:edit')->setName('admin_tags_edit');
                $app->post('', '\Blog\Controller\Admin\TagsController:post')->setName('admin_tags_post');
                $app->put('/{id:[0-9]+}', '\Blog\Controller\Admin\TagsController:update')->setName('admin_tags_update');
                $app->delete('/{id:[0-9]+}', '\Blog\Controller\Admin\TagsController:delete')->setName('admin_tags_delete');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

            // admin/articles routes
            $app->group('/data', function () use ($app) {

                $app->map(['GET', 'POST'], '/import', '\Blog\Controller\Admin\DataController:import')->setName('admin_data_import');

            })->add( new \Auth\Middleware\RoleAccess($container, [ User::ROLE_ADMIN ]) );

        })->add( new \Auth\Middleware\Auth( $container['auth'] ) ); // user must be authenticated
    }
}
