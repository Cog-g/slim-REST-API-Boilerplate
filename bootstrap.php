<?php
require_once dirname(__FILE__).'/vendor/autoload.php';

use Slim\Slim;

// Init application mode
if (empty($_SERVER['HTTP_SLIM_MODE'])) {
    $_SERVER['HTTP_SLIM_MODE'] = (getenv('HTTP_SLIM_MODE')) ? getenv('SLIM_MODE') : 'development';
}

// Init and load configuration
$config = array();

$configFile = dirname(__FILE__).'/app/share/config/'.$_SERVER['HTTP_SLIM_MODE'].'.php';

if (is_readable($configFile)) {
    require_once $configFile;
} else {
    require_once dirname(__FILE__).'/app/share/config/default.php';
}

// Create Application
$app = new Slim($config['app']);


// Only invoked if mode is "production"
$app->configureMode('production', function () use ($app) {
    $app->config(array(
        'log.enable' => TRUE, 'log.level' => \Slim\Log::WARN, 'debug' => FALSE
    ));
});

// Only invoked if mode is "development"
$app->configureMode('development', function () use ($app) {
    $app->config(array(
        'log.enable' => TRUE, 'log.level' => \Slim\Log::DEBUG, 'debug' => TRUE
    ));
});


// Header
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Headers: auth-id,auth-session,auth-token');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS'); // http://stackoverflow.com/a/7605119/578667
header('Access-Control-Max-Age: 86400');

// ------------------------------------------------------------
// Allow control of allow-origin header
// http://stackoverflow.com/a/8154264/2667369
// ------------------------------------------------------------

$app->options('/:w+', function() use ($app) {
    exit();
});

// Todo:
// Cache Middleware (inner)
// $app->add(new API\Middleware\Cache('/api/v1'));

// Parses JSON body
//$app->add(new \Slim\Middleware\ContentTypes());

// Manage Rate Limit
//$app->add(new API\Middleware\RateLimit('/api/v1'));
// JSON Middleware
//$app->add(new API\Middleware\JSON('/api/v1'));
// Auth Middleware (outer)
//$app->add(new API\Middleware\TokenOverBasicAuth(array('root' => '/api/v1')));
