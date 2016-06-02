<?php
/**
 * this file should be included when loading the module
 * it will have access to $app which can be passed on to the
 * following files too
 */

namespace Hello;

use Slim\App;
use MartynBiz\Slim3Module\AbstractModule;

class Module extends AbstractModule
{
    /**
     * Load is run last, when config, dependencies, etc have been initiated
     * Routes ought to go here
     * @param App $app
     * @return void
     */
     public static function initRoutes(App $app)
     {
         $app->get('/hello/{name}', function ($request, $response) {
             $name = $request->getAttribute('name');
             $response->getBody()->write("Hello, $name");

             return $response;
         });
     }
}
