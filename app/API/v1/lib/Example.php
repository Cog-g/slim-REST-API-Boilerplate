<?php
/**
 * Class Campaigns
 *
 * @package API\v1\lib
 * @author  Constantin Guay <cguay@netmediaeurope.com>
 */

namespace API\v1\lib;

class Example extends RestApiInterface
{
    protected function __construct()
    {
        parent::__construct();

        // todo: do something...
    }


    public function find()
    {
        $campaignList = DB::getInstance()->find(
            "SELECT ID, Name, Created, Edited FROM my_table WHERE my_field = :my_value",
            array(
                'my_value' => 'myValue'
            ),
            array($this->defaultOrderByField, $this->defaultOrderBySort),
            array($this->defaultLimit)
        );

        return $campaignList;
    }


    public function findOne($id) {
        $campaignList = DB::getInstance()->findOne(
            "SELECT ID, Name, Created, Edited FROM my_table WHERE my_field = :my_value",
            array(
                'my_value' => 'myValue'
            ),
            array($this->defaultOrderByField, $this->defaultOrderBySort)
        );

        return $campaignList;
    }

    public function insert($data) { }

    public function findAndModify($id, $data) { }

    public function remove($id) { }

    public function validate($data) { }
}
