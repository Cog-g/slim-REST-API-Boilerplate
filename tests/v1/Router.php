<?php
namespace API\v1\Tests;

use API\v1\RestApi;
use Slim\Environment;
use Slim\Slim;


/**
 * Class RouteTest
 * From https://github.com/there4/slim-test-helpers/blob/master/src/There4/Slim/Test/WebTestClient.php
 * @method get
 * @method post
 * @method put
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{

    public $app;
    public $request;
    public $response;

    public $methodsAllowed = ['get', 'post', 'put'];


    /**
     * @param $method
     * @param $arguments
     *
     * @return string
     */
    public function __call($method, $arguments)
    {
        $method = strtolower($method);
        if (in_array($method, $this->methodsAllowed)) {
            list($path, $data, $headers) = array_pad($arguments, 3, []);

            return $this->request($method, $path, $data, $headers);
        }
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }


    public function request($method, $path, $data = [], $headers = [])
    {
        // Reset
        RestApi::$returnCode = 200;

        // Capture STDOUT
        ob_start();

        $options = [
            'REQUEST_METHOD' => strtoupper($method),
            'PATH_INFO' => $path,
            'SERVER_NAME' => 'local.dev',
            'CONTENT_TYPE' => !empty($headers['Content-Type']) ? $headers['Content-Type'] : 'application/json'
        ];

        if ('get' === $method) {
            $options['QUERY_STRING'] = http_build_query($data);
        }
        elseif (is_array($data)) {
            $options['slim.input'] = http_build_query($data);
        }
        else {
            $options['slim.input'] = $data;
        }

        // Prepare a mock environment
        Environment::mock(array_merge($options, $headers));

        $config = [];
        require PROJECT_ROOT . '/tests/v1/config.php';

        $app = new Slim($config['app']);

        require PROJECT_ROOT . '/www-docs/app/v1/routes.php';

        $this->app = $app;
        $this->request = $app->request();
        $this->response = $app->response();

        // Run the app
        $this->app->run();

        // Return the application output. Also available in `response->body()`
        return ob_get_clean();
    }


    public function testIndex()
    {
        $this->get('/');
        $this->assertEquals(404, $this->response->{'status'}());
    }
}
