<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Filesystem\Filesystem;

class PostVersionRemove
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $app    = $this->getQueryParamApp($request);
        $queryParams = $request->getQueryParams();
        $params = $request->getParsedBody();

        if (!$app) {
            $response->withStatus(404)
                     ->getBody()
                     ->write('App not found');
            return $response;
        }

        if (!isset($params['version']) || !$params['version']) {
            $response->getBody()
                     ->write('Version name is required');
            return $response->withStatus(400);
        }

        $version  = $params['version'];
        $versions = $this->appManager->getAppVersions($queryParams['app']);
        if (!in_array($version, $versions)) {
            $response->getBody()
                     ->write('Version name is not existed.');
            return $response->withStatus(404);
        }

        $filesystem = new Filesystem();
        $filesystem->remove($app['path'] . DIRECTORY_SEPARATOR . $version);

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }

}
