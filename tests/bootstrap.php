<?php
// Settings to make all errors more obvious during testing
error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

session_start();

define('APP_API_REVISION', 'testing');

define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
define('APP_ROOT_PATH', realpath(dirname(__DIR__)) . '/www-docs');
?>
    --------------------------------------------------------------
    Unit tests
    ==============================================================
<?php
