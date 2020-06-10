<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class GetVersions
{
    use UseViewService, UseAppManagerService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $app = $this->getQueryParamApp($request);
        $queryParams = $request->getQueryParams();

        if (!$app) {
            $response->withStatus(404)
                     ->getBody()
                     ->write('App not found');
            return $response;
        }

        $view     = $this->view;
        $versions = $this->appManager->getAppVersions($queryParams['app']);

        $response->getBody()->write(
            $view->render('versions', ['app' => $app, 'appId' => $queryParams['app'], 'versions' => $versions])
        );
        return $response;
    }
}
