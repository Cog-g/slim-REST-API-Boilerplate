<?php
define('SLIM_API_START_TS', microtime(true));

require_once dirname(__FILE__) . '/../bootstrap.php';

use API\v1;
use API\Exception;


$app->group('/api/v1', function() use ($app) {

    $revision = '';

    if (file_exists(dirname(__FILE__).'/../REVISION')) {
        // For deployment by Capistrano, we get
        // the commit ref to add it to version number.
        $revision = '-rev.'.substr(file_get_contents(dirname(__FILE__).'/../REVISION'), 0, 8);
    }

    // Todo: Replace by current version of your API
    define('SLIM_API_REVISION', '1.0.0-alpha.1'.$revision);

    require_once(dirname(__FILE__) . '/../app/API/v1/routes/index.php');

    $app->get('/', function() {

        print('{"version":"'.SLIM_API_REVISION.'"}');

    });

});

$app->run();
