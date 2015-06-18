<?php
// ------------------------------------------------------------
// Common configuration
// ------------------------------------------------------------

$config['app']['mode'] = 'test';
$config['app']['debug'] = true;

// Cache TTL in seconds
$config['app']['cache.ttl'] = 0; // seconds

// Max requests per hour
$config['app']['rate.limit'] = 1000;

\API\v1\DB::$DBHost = 'localhost';
\API\v1\DB::$DBName = 'unit_test';
\API\v1\DB::$DBUser = 'unit_test';
\API\v1\DB::$DBPass = 'unit_test';
