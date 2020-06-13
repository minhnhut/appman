<?php

namespace App\Handlers;

use App\Config;
use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class PostEdit
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $config            = $this->config;
        $queryParams       = $request->getQueryParams();
        $params            = $request->getParsedBody();

        $app               = $this->appManager->getApp($queryParams['app']);

        if (!$app) {
            $response->withStatus(404)
                     ->getBody()
                     ->write('App not found');
            return $response;
        }

        $params['extra_links'] =  array_map(
            function ($v) {return trim($v);},
            explode("\n", $params['extra_links']));

        $params['ignore_files'] =  array_map(
            function ($v) {return trim($v);},
            explode("\n", $params['ignore_files']));

        $config->updateApp($queryParams['app'], $params);

        return $response->withHeader('Location', "/versions?app={$queryParams['app']}&success=1")
                        ->withStatus(302);
    }
}
