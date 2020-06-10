<?php

namespace App\HandlerTraits;

use App\AppManager;
use DI\Container;

trait UseAppManagerService
{
    /** @var AppManager */
    protected $appManager;

    private function useAppManager(Container $container)
    {
        $this->appManager = $container->get('appManager');
    }
}
