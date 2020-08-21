<?php

if (PHP_SAPI == 'cli-server') {

    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];

    // check the file types, only serve standard files
    if (preg_match('/\.(?:png|js|jpg|jpeg|gif|css)$/', $file)) {
        // does the file exist? If so, return it
        if (is_file($file))
            return false;

        // file does not exist. return a 404
        header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
        printf('"%s" does not exist', $_SERVER['REQUEST_URI']);
        return false;
    }
}

use App\AppManager;
use App\Config;
use App\Handlers\GetLogin;
use App\Handlers\GetVersionAdd;
use App\Handlers\GetVersions;
use App\Handlers\PostDeregister;
use App\Handlers\PostEdit;
use App\Handlers\PostLogin;
use App\Handlers\PostLogout;
use App\Handlers\PostSetup;
use App\Handlers\PostVersionAdd;
use App\Handlers\PostVersionRemove;
use App\Handlers\PostVersionSwitch;
use App\Middlewares\AuthMiddleware;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Middleware\Session;
use SlimSession\Helper;

require __DIR__ . '/../vendor/autoload.php';

// Create Container using PHP-DI
$container = new Container();
$container->set('config', function() {
    return new Config();
});
$container->set('view', function() {
    $engine = new League\Plates\Engine(__DIR__ . '/../src/views/pages');
    $engine->addFolder('partial', __DIR__ . '/../src/views/partial');
    $engine->registerFunction('form', function() {
        return new Formr();
    });
    return $engine;
});
$container->set('appManager', function() use ($container) {
   return new AppManager($container->get('config'));
});
// Register globally to app
$container->set('session', function () {
    return new Helper();
});

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->add($container->make(AuthMiddleware::class));
$app->add(
    new Session([
        'name' => 'main_session',
        'autorefresh' => true,
        'lifetime' => '1 hour',
    ])
);

$app->get('/', function (Request $request, Response $response, $args) {
    $view       = $this->get('view');
    /** @var AppManager $appManager */
    $appManager = $this->get('appManager');
    $apps       = $appManager->getApps();

    $response->getBody()->write(
        $view->render('apps', ['apps' => $apps])
    );
    return $response;
});

$app->get('/setup', function (Request $request, Response $response, $args) {
    $view = $this->get('view');
    $response->getBody()->write(
        $view->render('setup')
    );
    return $response;
});

$app->get('/view', function (Request $request, Response $response, $args) {
    $config     = $this->get('config');
    $params     = $request->getQueryParams();
    $view       = $this->get('view');

    $appName    = $params['app'];
    /** @var AppManager $appManager */
    $appManager = $this->get('appManager');
    $app        = $appManager->getApp($appName);

    if (!$app) {
        $response->getBody()->write(
            "App not found"
        );
        return $response->withStatus(404);
    }

    $response->getBody()->write(
        $view->render('edit', ['app' => $app, 'success' => isset($params['success'])])
    );

    return $response;
});

$app->post('/setup', PostSetup::class);
$app->post('/view', PostEdit::class);

$app->post('/deregister', PostDeregister::class);

$app->get('/versions', GetVersions::class);
$app->get('/version_add', GetVersionAdd::class);
$app->post('/version_add', PostVersionAdd::class);
$app->post('/version_remove', PostVersionRemove::class);
$app->post('/version_switch', PostVersionSwitch::class);

// API
$app->post('/api/version_add', \App\Handlers\Api\PostVersionAdd::class);
$app->post('/api/version_refresh', \App\Handlers\Api\PostVersionRefresh::class);

$app->get('/login', GetLogin::class);
$app->post('/login', PostLogin::class);
$app->get('/logout', PostLogout::class);

$app->run();
