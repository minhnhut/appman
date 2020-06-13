<?php

namespace App\HandlerTraits;

use App\AppManager;
use DI\Container;
use SlimSession\Helper;

trait UseSession
{
    /** @var Helper */
    protected $session;

    private function useSession(Container $container)
    {
        $this->session = $container->get('session');
    }
}
