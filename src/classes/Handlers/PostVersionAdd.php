<?php

namespace App\Handlers;

use App\Deployers\DeployerInterface;
use App\Deployers\DeployLocalZipFile;
use App\Deployers\DeployS3File;
use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Component\Filesystem\Filesystem;

class PostVersionAdd
{
    use UseViewService, UseAppManagerService, UseConfigService, GetQueryParamApp;

    const DEPLOY_STRATEGY_UPLOAD     = "upload";
    const DEPLOY_STRATEGY_S3_PULL    = "s3_pull";
    const DEPLOY_STRATEGY_HTTP_PULL  = "http_pull";
    const DEPLOY_STRATEGY_SFTP_PULL  = "sftp_pull";

    protected $container;

    public function __construct(Container $container) {
        $this->useView($container);
        $this->useAppManager($container);
        $this->useConfig($container);
        $this->container = $container;
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

        $forceDeploy    = $params['force'] ?? 0;
        $version        = $params['version'];
        $versions       = $this->appManager->getAppVersions($queryParams['app']);
        if (in_array($version, $versions)) {
            if ($forceDeploy) {
                $filesystem = new Filesystem();
                $filesystem->remove($app['path'] . DIRECTORY_SEPARATOR . $version);
            } else {
                $response->getBody()
                         ->write('Version name is already existed.');
                return $response->withStatus(400);
            }
        }

        switch ($params['deploy_strategy']) {
            case self::DEPLOY_STRATEGY_UPLOAD:
                return $this->handleFileUploadDeploy($request, $response);
            case self::DEPLOY_STRATEGY_S3_PULL:
                return $this->handleS3PullDeploy($request, $response);
        }

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }

    private function handleFileUploadDeploy(Request $request, Response $response)
    {
        $queryParams = $request->getQueryParams();
        $params      = $request->getParsedBody();

        /** @var UploadedFileInterface[] $files */
        $files = $request->getUploadedFiles();
        $tempFolder = $this->config->getTempPath();
        $archives = array_map(function ($file) use ($tempFolder) {
            $archivePath = $tempFolder . '/' . time() . '_' . $file->getClientFilename();
            $file->moveTo($archivePath);
            return $archivePath;
        }, $files);

        $version  = $params['version'];

        /** @var DeployerInterface $deployer */
        $deployer = $this->container->make(DeployLocalZipFile::class);
        $deployer->deploy($queryParams['app'], $version, $archives);

        foreach ($archives as $archivePath) {
            unlink($archivePath);
        }

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }

    private function handleS3PullDeploy(Request $request, Response $response)
    {
        $queryParams = $request->getQueryParams();
        $params      = $request->getParsedBody();

        $version  = $params['version'];

        // TODO validate $params['s3_url'];

        /** @var DeployerInterface $deployer */
        $deployer = $this->container->make(DeployS3File::class);
        $deployer->deploy($queryParams['app'], $version, [$params['s3_url']]);

        return $response->withHeader('Location', '/versions?app='.$queryParams['app'])
                        ->withStatus(302);
    }
}
