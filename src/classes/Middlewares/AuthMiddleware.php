<?php

namespace App\Middlewares;

use App\Config;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SlimSession\Helper;

class AuthMiddleware
{

    protected $session;
    public function __construct(Helper $session) {
        $this->session = $session;
    }

    /**
     * Called when middleware needs to be executed.
     *
     * @param Request        $request PSR7 request
     * @param RequestHandler $handler PSR7 handler
     *
     * @return ResponseInterface|Response
     */
    public function __invoke(
        Request $request,
        RequestHandler $handler
    )
    {
        $path = $request->getUri()->getPath();
        $auth = $this->session->get('auth', '');
        if (!$auth && $path !== '/login' && strpos($path, '/api') !== 0) {
            $response = new Response();
            return $response->withStatus(301)->withHeader('Location', '/login');
        }

        return $handler->handle($request);
    }
}
