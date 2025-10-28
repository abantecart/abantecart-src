<?php
/*
 *   $Id$
 *
 *   AbanteCart, Ideal OpenSource Ecommerce Solution
 *   http://www.AbanteCart.com
 *
 *   Copyright © 2011-2025 Belavier Commerce LLC
 *
 *   This source file is subject to Open Software License (OSL 3.0)
 *   License details are bundled with this package in the file LICENSE.txt.
 *   It is also available at this URL:
 *   <http://www.opensource.org/licenses/OSL-3.0>
 *
 *  UPGRADE NOTE:
 *    Do not edit or add to this file if you wish to upgrade AbanteCart to newer
 *    versions in the future. If you wish to customize AbanteCart for your
 *    needs, please refer to http://www.AbanteCart.com for more information.
 */
const DS = DIRECTORY_SEPARATOR;
require __DIR__ . DS . '..' . DS . 'vendor' . DS . 'autoload.php';

use app\Middleware\JwtAuthMiddleware;
use Neomerx\Cors\Analyzer;
use Neomerx\Cors\Strategies\Settings;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Relay\Relay;
use Middlewares\ErrorHandler;
use Middlewares\JsonPayload;
use Middlewares\ContentType;
use Middlewares\Cors;

// PSR-7 factories
$psr17 = new Psr17Factory();
$serverRequest = (new ServerRequestCreator($psr17, $psr17, $psr17, $psr17))->fromGlobals();
$subPath = dirname($serverRequest->getServerParams()['SCRIPT_NAME']);

// Register PSR-4 autoloader
spl_autoload_register(function ($class) {
    $prefix = 'app\\';
    $base_dir = __DIR__ . DS . 'app' . DS;

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', DS, $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Bootstrap AbanteCart Registry
/** @var Registry $registry */
require __DIR__ . DS . 'bootstrap' . DS . 'abantecart.php';
//TODO: get token from header and detect what side was requested? admin or sf
//set only admin side yet
$_GET['s'] = ADMIN_PATH;

$dispatcher = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) use ($serverRequest) {
    //admin routes only yet
    require __DIR__ . DS . 'routes' . DS . 'admin.v1.php';
});

// Middleware: CORS, JSON, error, routing
$routeMw = function ($request, $next) use ($dispatcher, $psr17, $registry) {
    $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

    switch ($routeInfo[0]) {
        case FastRoute\Dispatcher::NOT_FOUND:
            return json($psr17, 404, ['error' => ['code' => 'not_found', 'message' => 'Not Found']]);
        case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
            return json($psr17, 405, ['error' => ['code' => 'method_not_allowed', 'message' => 'Method Not Allowed']]);
        case FastRoute\Dispatcher::FOUND:
            [$handler, $vars] = [$routeInfo[1], $routeInfo[2]];
            [$class, $method] = $handler;
            $instance = new $class($registry);
            //call controller
            return $instance->$method($request, $vars, $psr17);
    }
    return json($psr17, 404, ['error' => ['code' => 'not_found', 'message' => 'Not Found']]);
};

$errorHandler = new ErrorHandler();
$errorHandler = $errorHandler->addFormatters();

$serverParams = $serverRequest->getServerParams();
$corsSettings = (new Settings())
    ->setServerOrigin(
        $serverParams['HTTPS'] ? 'https' : 'http',
        $serverParams['SERVER_NAME'] ?? 'localhost',
        $serverParams['SERVER_PORT'] ?? ($serverParams['HTTPS'] ? 443 : 80)
    )
    ->setAllowedOrigins(['*'])
    ->setAllowedHeaders(['Content-Type', 'Authorization', 'Accept-Language', 'X-Currency', 'X-Channel'])
    ->setAllowedMethods(['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'])
    ->setExposedHeaders([])
    ->setCredentialsNotSupported()
    ->setPreFlightCacheMaxAge(600)
    ->enableCheckHost();

$corsAnalyzer = Analyzer::instance($corsSettings);

// JWT secret key (TODO: move to config)
$jwtSecretKey = 'your-secret-key-here';

// Paths that don't require authentication
$excludedPaths = [
    'admin/v1/login',
    'admin/v1/auth',
    'admin/v1/products',
    // add other public endpoints here
];
//add subdirectory
$excludedPaths = array_map(function ($path) use ($subPath) {
    return $subPath . '/' . $path;
}, $excludedPaths);

$queue = [
    $errorHandler,
    new Cors($corsAnalyzer),
    new JsonPayload(),           // parsing JSON body
    new JwtAuthMiddleware($psr17, $jwtSecretKey, $excludedPaths), // JWT Auth middleware
    $routeMw,
];

$relay = new Relay($queue);
$response = $relay->handle($serverRequest);
send($response);

// helpers
function json($psr17, int $code, array $data)
{
    $res = $psr17->createResponse($code);
    $res->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $res->withHeader('Content-Type', 'application/json');
}

function send($response)
{
    http_response_code($response->getStatusCode());
    foreach ($response->getHeaders() as $n => $vals) foreach ($vals as $v) header("$n: $v", false);
    echo $response->getBody();
}


