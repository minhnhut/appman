<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetLogin
{
    use UseViewService, UseAppManagerService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $view = $this->view;
        $response->getBody()->write(
            $view->render('login')
        );
        return $response;
    }
}
