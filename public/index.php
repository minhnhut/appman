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

use App\Config;
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
   return new League\Plates\Engine(__DIR__ . '/../src/views');
});

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

$app->get('/', function (Request $request, Response $response, $args) {
    $config = $this->get('config');
    $view = $this->get('view');

    $apps        = $config->getConfig("apps");
    $appsPath    = $config->getGlobalConfig(Config::APPS_FOLDER);
    $currentName = $config->getGlobalConfig(Config::CURRENT_FOLDER_NAME);

    $apps = array_map(function($app) use ($currentName) {
        $path    = implode(DIRECTORY_SEPARATOR, [$app['path'], $currentName]);
        $current = readlink($path);
        if ($current) {
            $app['version'] = $current;
        } else {
            $app['version'] = "Unknown";
        }
        return $app;
    }, $apps);

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

$app->post('/setup', function (Request $request, Response $response, $args) {
    $config = $this->get('config');
    $params = $request->getParsedBody();

    // TODO Validation
    if (!$params['version']) {
        $params['version'] = 'v0.0';
    }

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

    if (!is_dir($appPath)) {
        if (!$createPath) {
            $response->withStatus(500)
                     ->getBody()
                     ->write('App path is not a directory');
            return $response;
        }
        mkdir($createPath, 0777, true);
    }

    chdir   ($appPath);
    mkdir   ($commonFolderName);
    mkdir   ($version);
    symlink ($version, $currentFolderName);

    $response->withHeader('Location', '/');
    return $response;
});

$app->run();
