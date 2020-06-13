<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseConfigService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostDeregister
{

    use UseConfigService, GetQueryParamApp;

    public function __construct(Container $container)
    {
        $this->useConfig($container);
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $config = $this->config;
        $params = $request->getQueryParams();
        $name   = $params['app'];

        $config->deregisterApp($name);

        return $response->withHeader('Location', '/')
                        ->withStatus(302);
    }
}
