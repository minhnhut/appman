<?php

namespace App\Handlers;

use App\HandlerTraits\GetQueryParamApp;
use App\HandlerTraits\UseAppManagerService;
use App\HandlerTraits\UseConfigService;
use App\HandlerTraits\UseSession;
use App\HandlerTraits\UseViewService;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class PostLogin
{
    use UseConfigService, UseSession, GetQueryParamApp;

    public function __construct(Container $container) {
        $this->useConfig($container);
        $this->useSession($container);
    }

    public function __invoke (Request $request, Response $response, $args) {

        $params = $request->getParsedBody();

        $username = $params['username'];
        $password = $params['password'];

        $config = $this->config;
        $goodUserName = $config->getGlobalConfig('auth_username', 'admin');
        $goodPassword = $config->getGlobalConfig('auth_password', 'password');

        if ($username == $goodUserName && $password == $goodPassword) {
            $this->session->set('auth', 1);
            return $response->withStatus(302)->withHeader('Location', '/');
        }

        return $response->withStatus(302)->withHeader('Location', '/login');
    }
}
