<?php
require_once dirname(__FILE__) . '/../bootstrap.php';


$app->group('/api/v1', function () use ($app) {

    $revision = '';

    if (file_exists(dirname(__FILE__) . '/../REVISION')) {
        // For deployment by Capistrano, we get
        // the commit ref to add it to version number.
        $revision = '-rev.' . substr(file_get_contents(dirname(__FILE__) . '/../REVISION'), 0, 8);
    }

    // Todo: Replace by current version of your API
    define('APP_API_REVISION', APP_API_VERSION_V1 . $revision);

    require_once(dirname(__FILE__) . '/../app/v1/routes.php');

    $app->get('/', function () {

        print('{"version":"' . APP_API_REVISION . '"}');

    });

});

$app->run();
