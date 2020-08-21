<?php

namespace App\Handlers\Api;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class PostVersionRefresh
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    protected $container;

    public function __construct(Container $container) {
        $this->container = $container;
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $app         = $this->getQueryParamApp($request);
        $queryParams = $request->getQueryParams();
        $params      = $request->getParsedBody();

        $key = $params['key'] ?? $queryParams['key'] ?? "";

        if (!$app) {
            $response->withStatus(404)
                     ->getBody()
                     ->write(
                         json_encode([
                             'error' => 'App not found'
                         ])
                     );
            return $response;
        }

        if (!empty($app['api_key']) && $key == $app['api_key']) {
            $version        = $params['version'];
            $this->appManager->switchVersion($queryParams['app'], $version);
            $response->withStatus(200)
                     ->getBody()
                     ->write(
                         json_encode([
                             'message' => 'OK'
                         ])
                     );
            return $response;
        }

        $response
             ->getBody()
             ->write(
                 json_encode([
                     'error' => 'Requested operation is not allowed.'
                 ])
             );

        return $response->withStatus(400);

    }
}
