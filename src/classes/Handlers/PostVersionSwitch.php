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

class PostVersionSwitch
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $app = $this->getQueryParamApp($request);
        $queryParams = $request->getQueryParams();
        $params      = $request->getParsedBody();

        if (!$app) {
            $response->withStatus(404)
                     ->getBody()
                     ->write('App not found');
            return $response;
        }

        $versions = $this->appManager->getAppVersions($queryParams['app']);
        $version  = $params['version'];
        $config            = $this->config;
        $currentFolderName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
        $commonFolderName  = $config->getGlobalConfig(Config::COMMON_FOLDER_NAME);

        if (!in_array($version, $versions)) {
            $response->withStatus(404)
                     ->getBody()
                     ->write('Version not found');
            return $response;
        }

        chdir   ($app['path']);
        unlink  ($currentFolderName);
        @symlink($version, $currentFolderName);

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }
}
