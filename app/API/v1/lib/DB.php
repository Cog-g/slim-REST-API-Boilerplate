<?php
/**
 * Class DB
 * @package API\v1\Database
 * @author Constantin Guay <cguay@netmediaeurope.com>
 */

namespace API\v1\lib;

use API\Exception\ValidationException;

class DB extends Singleton
{
    protected static $dbh; // database handler


    protected function __construct($host = null, $dbName = null, $dbUser = null, $dbPass = null)
    {
        self::$dbh = new \PDO('mysql:host='.DBHOST.';dbname='.DBNAME.';charset=utf8', DBUSER, DBPASS);
        self::$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }


    /**
     * @param string $sql
     * @param array $params
     * @param array $sort
     * @param array $limit
     * @return array
     * @throws \API\Exception\ValidationException
     */
    public function find($sql, $params = array(), $sort = array(), $limit = array())
    {
        if (empty($sql)) {
            throw new ValidationException('Empty query');
        }

        $ORDERBY = (empty($sort[0]) || empty($sort[1])) ? '' : ' ORDER BY `'.$sort[0].'` '.$sort[1];
        $LIMIT  = (empty($limit) || empty($limit[0])) ? '' : ' LIMIT '.$limit[0].( (!isset($limit[1])) ? '' : ', '.$limit[1] );

        $stmt = self::$dbh->prepare($sql.$ORDERBY.$LIMIT);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    /**
     * @param string $sql
     * @param array $params
     * @param array $sort
     * @return mixed
     * @throws \API\Exception\ValidationException
     */
    public function findOne($sql, $params = array(), $sort = array())
    {
        if (empty($sql)) {
            throw new ValidationException('Empty query');
        }

        $SORTBY = (empty($sort)) ? '' : ' SORT BY '.$sort[0].' '.$sort[1];

        $stmt = self::$dbh->prepare($sql.$SORTBY);
        $stmt->execute($params);

        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
