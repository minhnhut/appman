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

class PostSetup
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
    }

    public function __invoke (Request $request, Response $response, $args) {
        $config            = $this->config;
        $params            = $request->getParsedBody();

        // TODO Validation
        if (!$params['version']) {
            $params['version'] = 'v0.0';
        }

        $name              = $params['name'];
        $version           = $params['version'];
        $appPath           = $params['path'];
        $createPath        = isset($params['create_path']);
        $currentFolderName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
        $commonFolderName  = $config->getGlobalConfig(Config::COMMON_FOLDER_NAME);

        if (!$appPath) {
            $response->withStatus(500)
                     ->getBody()
                     ->write('Invalid app path: ' . $appPath);
            return $response;
        }

        $pathCreated = true;
        if (!is_dir($appPath)) {
            if (!$createPath) {
                $response->withStatus(500)
                         ->getBody()
                         ->write('App path is not a directory');
                return $response;
            }
            $pathCreated = mkdir($appPath, 0777, true);
        }

        if (!$pathCreated) {
            $response->withStatus(500)
                     ->getBody()
                     ->write('Unable to create app path: ' . $appPath);
            return $response;
        }

        $params['extra_links'] =  array_map(
            function ($v) {return trim($v);},
            explode("\n", $params['extra_links']));

        $params['ignore_files'] =  array_map(
            function ($v) {return trim($v);},
            explode("\n", $params['ignore_files']));

        chdir   ($appPath);
        @mkdir  ($commonFolderName);
        @mkdir  ($version);
        @symlink($version, $currentFolderName);
        unset($params['submit']);
        $config->addApp($name, $appPath, $params);

        return $response->withHeader('Location', '/')
                        ->withStatus(302);
    }
}
