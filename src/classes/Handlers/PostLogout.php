<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseSession;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostLogout
{
    use UseSession, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useSession($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $this->session->clear();
        return $response->withStatus(302)->withHeader('Location', '/login');
    }
}
