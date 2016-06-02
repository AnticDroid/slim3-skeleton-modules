<?php
namespace Auth\Middleware;

use Auth\Auth as AuthService;

class AdminOnly
{
    /**
     * @var App\Auth\Auth
     */
    protected $auth;

    public function __construct(AuthService $auth)
    {
        $this->auth = $auth;
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
        $currentUser = $this->auth->getCurrentUser();
        if (! $currentUser->isAdmin() ) {
            throw new PermissionDenied('Permission denied to manage users.');
        }

        // pass onto the next callable
        $response = $next($request, $response);

        return $response;
    }
}
