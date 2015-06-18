<?php
// ------------------------------------------------------------
// Init.
// ------------------------------------------------------------

require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Slim\Slim;

define('APP_API_VERSION_V1', '1.0.0-alpha.1');
define('APP_API_START_TS', microtime(true));
define('APP_ROOT_PATH', realpath(dirname(__DIR__)) . '/www-docs');

// Init application mode
$ENVIRONMENT = file_exists(APP_ROOT_PATH . '/../ENVIRONMENT') ? file_get_contents(APP_ROOT_PATH . '/../ENVIRONMENT') :
    'sandbox';


// ------------------------------------------------------------
// configuration
// ------------------------------------------------------------

// Init and load configuration
$config = [];

// Config file should not be in git. You can find an example
// in tools/config-example.php
require_once APP_ROOT_PATH . '/app/v1/config.php';

// Create Application
$app = new Slim($config['app']);

// ------------------------------------------------------------
// Allow control of allow-origin header
// http://stackoverflow.com/a/8154264/2667369
// ------------------------------------------------------------

$app->options('/:w+', function() use ($app) {

    // Here you could define rules, but it's not mandatory.
    exit();

});


// ------------------------------------------------------------
// Add middleware
// ------------------------------------------------------------

// Cache Middleware (inner)
if (empty($app->request->headers['Cache-Control']) || 'no-cache' !== $app->request->headers['Cache-Control']) {
    $app->add(new API\v1\Middleware\Cache('/api/v1'));
}

// Manage Rate Limit
$app->add(new API\v1\Middleware\RateLimit('/api/v1'));
