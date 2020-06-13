<?php


namespace App\Deployers;

use DI\Container;

interface DeployerInterface
{
    public function __construct(Container $DIContainer);
    public function deploy($appId, $targetVersion, $targetPackage);
}
