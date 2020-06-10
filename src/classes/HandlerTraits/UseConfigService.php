<?php

namespace App\HandlerTraits;

use App\Config;
use DI\Container;

trait UseConfigService
{
    /** @var Config */
    protected $config;

    private function useConfig(Container $container)
    {
        $this->config = $container->get('config');
    }
}
