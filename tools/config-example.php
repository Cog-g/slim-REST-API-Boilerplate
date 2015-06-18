<?php
// ------------------------------------------------------------
// Configuration
// ------------------------------------------------------------

$config['app']['mode'] = 'development';
$config['app']['debug'] = true;

// Cache TTL in seconds
$config['app']['cache.ttl'] = 0; // seconds

// Max requests per hour
$config['app']['rate.limit'] = 1000;

\API\v1\DB::$DBHost = 'localhost';
\API\v1\DB::$DBName = 'myDatabase';
\API\v1\DB::$DBUser = 'myUser';
\API\v1\DB::$DBPass = '123456';
