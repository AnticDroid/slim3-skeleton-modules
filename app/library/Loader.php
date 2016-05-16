<?php

/**
 * This will load modules
 */
class Loader
{
    /**
     * @var Slim
     */
    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Load the module
     * @param string $name Module name
     */
    public function load($name)
    {
        // get the path of the modules
        $modulePath = realpath(APPLICATION_PATH . '/modules/' . $name);

        // register an autoloader for the module's library
        

        // include the required module.php path of that module
        require $modulePath . '/module.php';
    }
}
