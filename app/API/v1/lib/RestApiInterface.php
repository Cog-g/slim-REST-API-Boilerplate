<?php
/**
 * Class RestApiInterface
 *
 * @package API\v1
 * @author  Constantin Guay <cguay@netmediaeurope.com>
 */

namespace API\v1\lib;

use Slim\Slim;

abstract class RestApiInterface extends Singleton
{
    /** @var static DB $db */
    protected $db;
    /** @var static Slim $app */
    protected $app;

    protected $defaultLimit = 0; // 0 no limit
    protected $defaultOrderByField;
    protected $defaultOrderBySort;

    protected function __construct()
    {
        $this->db = DB::getInstance();
        $this->app = Slim::getInstance();


        // Order and Sort parameter
        // ******************************************
        $orderby = $this->app->request()->params('orderby');
        if (!empty($orderby)) {
            $this->defaultOrderByField = $orderby;
        }

        $sort = $this->app->request()->params('sort');
        if (!empty($sort)) {
            $this->defaultOrderBySort = $sort;
        } else if (!empty($this->defaultOrderByField)) {
            $this->defaultOrderBySort = 'ASC';
        }

        if ($this->defaultOrderBySort == 1) {
            $this->defaultOrderBySort = 'ASC';
        } else if ($this->defaultOrderBySort == -1) {
            $this->defaultOrderBySort = 'DESC';
        }


        // Limit parameter
        // ******************************************
        $limit = $this->app->request()->params('limit');
        if (!empty($limit)) {
            $this->defaultLimit = $limit;
        }
    }


    abstract public function find();
    abstract public function findOne($id);
    abstract public function insert($data);
    abstract public function findAndModify($id, $data);
    abstract public function remove($id);
    abstract public function validate($data);

    public function finalize($content, $status = 200)
    {
        $this->app->response->setStatus($status);
        $this->app->response->headers->set('X-API-Version', 'Slim-API/'.SLIM_API_REVISION);

        $theContent = new \stdClass();

        // format as needed
        if (is_array($content)) {
            $theContent->data = json_decode(json_encode($content));
        } else {
            $theContent->data = $content;
        }

        // common things
        $theContent->apiVersion = SLIM_API_REVISION;
        if ($status === 200) { $theContent->success = true; }
        else { $theContent->success = false; }

        // took
        if (defined('SLIM_API_START_TS')) {
            $theContent->took = round((microtime(true) - SLIM_API_START_TS) * 1000);
        }


        if ($this->app->config('format') === 'json') {
            $this->app->response->headers->set('Content-Type', 'application/json');
        } else {
            $this->app->response->headers->set('Content-Type', 'text/plain');
        }

        print json_encode($theContent);

        $this->app->stop();

        return true;
    }
}
