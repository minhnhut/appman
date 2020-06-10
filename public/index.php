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
use App\Handlers\GetVersionAdd;
use App\Handlers\GetVersions;
use App\Handlers\PostVersionAdd;
use App\Handlers\PostVersionSwitch;
use DI\Container;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

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

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

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
        $view->render('edit', ['app' => $app])
    );

    return $response;
});

$app->post('/setup', function (Request $request, Response $response, $args) {
    $config            = $this->get('config');
    $params            = $request->getParsedBody();
    $queryParams       = $request->getQueryParams();

    $appId = isset($queryParams['app']) ? $queryParams['app'] : null;

    // TODO Validation
    if (!$params['version']) {
        $params['version'] = 'v0.0';
    }

    $name              = $params['app_name'];
    $version           = $params['version'];
    $appPath           = $params['path'];
    $createPath        = isset($params['create_path']);
    $currentFolderName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);
    $commonFolderName  = $config->getGlobalConfig(Config::COMMON_FOLDER_NAME);

    if (!$appPath) {
        $response->withStatus(500)
                 ->getBody()
                 ->write('Invalid app path: ' . $appPath);
        return $response;
    }

    $pathCreated = true;
    if (!is_dir($appPath)) {
        if (!$createPath) {
            $response->withStatus(500)
                     ->getBody()
                     ->write('App path is not a directory');
            return $response;
        }
        $pathCreated = mkdir($appPath, 0777, true);
    }

    if (!$pathCreated) {
        $response->withStatus(500)
                 ->getBody()
                 ->write('Unable to create app path: ' . $appPath);
        return $response;
    }

    chdir   ($appPath);
    @mkdir  ($commonFolderName);
    @mkdir  ($version);
    @symlink($version, $currentFolderName);
    $config->addApp($name, $appPath);

    return $response->withHeader('Location', '/')
                    ->withStatus(302);
});

$app->get('/versions', GetVersions::class);
$app->get('/version_add', GetVersionAdd::class);
$app->post('/version_add', PostVersionAdd::class);
$app->post('/version_switch', PostVersionSwitch::class);

$app->run();
