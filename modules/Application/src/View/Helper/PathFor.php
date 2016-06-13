<?php
namespace Application\View\Helper;

class PathFor
{
    /**
     * Slim\Container
     */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    function __invoke($routeName, $args=[])
    {
        return $this->container['router']->pathFor($routeName, $args);
    }
}
