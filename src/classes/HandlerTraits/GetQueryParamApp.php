<?php

namespace App\HandlerTraits;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

trait GetQueryParamApp
{
    protected function getQueryParamApp(Request $request)
    {
        if (!property_exists($this, 'appManager')) {
            throw new \BadMethodCallException(
                'GetQueryParamApp required appManager property. Consider use UseAppManagerService trait.'
            );
        }

        $queryParams    = $request->getQueryParams();
        if (!isset($queryParams['app'])) {
            return null;
        }
        $appId          = $queryParams['app'];
        $app            = $this->appManager->getApp($appId);

        if (!$app) {
            return null;
        }

        return $app;
    }
}
