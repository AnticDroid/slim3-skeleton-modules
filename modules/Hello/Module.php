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
     * This method is run last, when dependencies, middleware etc have been initiated
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
