<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;

class PostVersionAdd
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

        /** @var UploadedFileInterface[] $files */
        $files = $request->getUploadedFiles();
        $tempFolder = $this->config->getTempPath();
        $archives = array_map(function ($file) use ($tempFolder) {
            $archivePath = $tempFolder . '/' . time() . '_' . $file->getClientFilename();
            $file->moveTo($tempFolder . '/' . time() . '_' . $file->getClientFilename());
            return $archivePath;
        }, $files);

        if (!isset($params['version'])) {
            $response->getBody()
                     ->write('Version name is required');
            return $response->withStatus(400);
        }

        $version  = $params['version'];
        $versions = $this->appManager->getAppVersions($queryParams['app']);
        if (in_array($version, $versions)) {
            $response->getBody()
                     ->write('Version name is already existed.');
            return $response->withStatus(400);
        }

        chdir($app['path']);
        mkdir($version);

        $zip = new \ZipArchive();
        foreach ($archives as $archivePath) {
            echo $archivePath;
            $zip->open($archivePath);
            $zip->extractTo($version);
            $zip->close();
            unlink($archivePath);
        }

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }
}
