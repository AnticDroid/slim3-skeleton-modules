<?php
namespace Auth\Middleware;

use Slim\Container;
use Auth\Exception\PermissionDenied;

class RoleAccess
{
    /**
     * @var App\Auth\Auth
     */
    protected $auth;

    /**
     * @var array
     */
    protected $allowed;

    public function __construct(Container $container, $allowed=[])
    {
        if (! is_array($allowed))
            $allowed = [$allowed];

        $this->container = $container;
        $this->allowed = $allowed;
    }

    /**
     * Example middleware invokable class
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke($request, $response, $next)
    {
        $auth = $this->container['auth'];

        $currentUser = $auth->getCurrentUser();
        if (! in_array($currentUser->role, $this->allowed) ) {
            throw new PermissionDenied('Permission denied to access this resource.');
        }

        // pass onto the next callable
        $response = $next($request, $response);

        return $response;
    }
}
