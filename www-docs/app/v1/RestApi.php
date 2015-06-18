<?php
namespace API\v1;

use Slim\Slim;

abstract class RestApi extends Singleton
{

    /** @var static Slim $app */
    protected $app;

    public static $params;
    public static $returnCode = 200;
    public static $results = [];


    protected function __construct()
    {
        $this->app = Slim::getInstance();
    }


    abstract public function getList();


    public static function prepareIdsAsArray($ids)
    {
        return is_array($ids) ? $ids : [$ids];
    }


    public static function prepareIdAsUnique($id)
    {
        return 'all' !== $id ? intval($id) : 'all';
    }


    public function finalize(Slim $app, $content, $status = false)
    {
        // Allow to override the status, or to set it from outside
        // $app.
        $status = $status ?: self::$returnCode;

        //var_dump($status); exit();
        $results = (object)[];


        // Format the data to an object, if needed.
        // ----------------------------------------
        if (is_a($content, '\Exception')) {
            Api::$debug[] = [
                $content->{'getMessage'}(),
                $content->{'getFile'}(),
                $content->{'getLine'}(),
            ];
            $results->results = json_decode(json_encode($content->{'getMessage'}()));
        }
        else if (is_array($content)) {
            $results->results = json_decode(json_encode($content));
        }
        else {
            $results->results = $content;
        }


        // Status code
        // ----------------------------------------
        if ($status >= 200 && $status < 300) {
            $results->success = true;
        }
        else {
            $results->error = $results->results;
            $results->success = false;
            unset($results->results);
        }


        // Version
        // ----------------------------------------
        if (defined('APP_API_REVISION')) {
            $results->version = APP_API_REVISION;
            $app->response->headers->set('X-API-Version', __NAMESPACE__ . '/' . APP_API_REVISION);
        }


        // Debug
        // ----------------------------------------
        if (!!self::$params['debug'] || $app->config('debug')) {
            $results->debug = Api::$debug;
            $results->debug['total memory usage (in M)'] = round(memory_get_usage(true) / 1024 / 1024, 2);
        }


        // Print the output in desired format
        // =======================================================================

        // Set the status
        $app->response->setStatus($status);

        if (false !== strpos($app->request->headers['Content-Type'], 'text/plain')) {
            $app->response->headers->set('Content-Type', 'text/plain');

            print($results->success ? (string)$results->results : (string)$results->error);
        }
        elseif (false !== strpos($app->request->headers['Content-Type'], 'text/csv')) {
            $app->response->headers->set('Content-Type', 'text/csv');

            print('CSV format is not yet available');
        }
        elseif (false !== strpos($app->request->headers['Content-Type'], 'text/html')) {
            $app->response->headers->set('Content-Type', 'text/html');

            print('<p>HTML format is not yet available</p>');
        }
        else {
            $app->response->headers->set('Content-Type', 'application/json');

            print json_encode($results);
        }


        $app->stop();

        return $results->success;

    }

}
