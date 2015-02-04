<?php
// This is the production config
// *****************************

$config['app']['mode'] = $_SERVER['HTTP_SLIM_MODE'];
$config['app']['debug'] = false;

$config['app']['format'] = 'json';

// Cache TTL in seconds
$config['app']['cache.ttl'] = 60;
// Max requests per hour
$config['app']['rate.limit'] = 1000;
// Auth
$config['app']['auth.ttl'] = 30;
// Cookie
$config['app']['cookies.path'] = '/';
$config['app']['cookies.domain'] = '.'; // todo: Adapt for your use
$config['app']['cookies.secure'] = false; // todo: do you need it?
$config['app']['cookies.secret_key'] = 'This is a secret.'; // Todo: change it


defined('DBHOST') || define('DBHOST', 'localhost'); // todo
defined('DBUSER') || define('DBUSER', ''); // todo
defined('DBPASS') || define('DBPASS', ''); // todo
defined('DBNAME') || define('DBNAME', ''); // todo
defined('SECRET') || define('SECRET', 'should-be-a-secret-key_CHANGE-ME!'); // todo
