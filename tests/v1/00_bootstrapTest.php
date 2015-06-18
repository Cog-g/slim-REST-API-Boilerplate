<?php
namespace API\v1\Tests;

define('APP_API_START_TS', microtime(true));

require_once PROJECT_ROOT . '/vendor/autoload.php';
require_once 'Router.php'; // Slim test implementation

class AppImplementationTest extends RouteTest
{

    public function testWrongPath()
    {
        $this->get('/api/v1/wrong_path');
        $this->assertEquals(404, $this->response->{'status'}());
    }


    public function testVersion()
    {
        $get = json_decode($this->get('/api/v1/version'));

        $this->assertEquals(200, $this->response->{'status'}());
        $this->assertEquals(APP_API_REVISION, $get->version);
    }

}
