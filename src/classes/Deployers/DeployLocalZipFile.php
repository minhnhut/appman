<?php

namespace App\Deployers;

use App\AppManager;
use DI\Container;

class DeployLocalZipFile implements DeployerInterface
{
    /** @var AppManager */
    protected $appManager;

    public function __construct(Container $container) {
        $this->appManager = $container->get("appManager");
    }

    public function deploy($appId, $targetVersion, $targetPackages)
    {
        $app = $this->appManager->getApp($appId);

        chdir($app['path']);
        mkdir($targetVersion);

        $zip = new \ZipArchive();
        foreach ($targetPackages as $archivePath) {
            echo $archivePath;
            $zip->open($archivePath);
            $zip->extractTo($targetVersion);
            $zip->close();
        }
    }
}
