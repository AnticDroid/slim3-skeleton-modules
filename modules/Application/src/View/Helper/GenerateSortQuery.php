<?php
namespace Application\View\Helper;

class GenerateSortQuery
{
    /**
     * Slim\Container
     */
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    function __invoke($property, $query)
    {
        // put sort property at start
        $query['sort'] = $property;

        // toggle query
        if (isset($query['dir'])) {
            if ($query['dir'] == 1) {
                $query['dir'] = -1;
            } else {
                $query['dir'] = 1;
            }
        } else {
            $query['dir'] = 1;
        }

        return http_build_query($query);
    }
}
