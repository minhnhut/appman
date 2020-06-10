<?php

namespace App\HandlerTraits;

use DI\Container;
use League\Plates\Engine;

trait UseViewService
{
    /** @var Engine */
    protected $view;

    private function useView(Container $container)
    {
        $this->view = $container->get('view');
    }
}
