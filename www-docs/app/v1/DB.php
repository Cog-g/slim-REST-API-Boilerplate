<?php
namespace Api\v1;

use Api\v1\Exception\Validation;

class DB
{
    public static $DBHost;
    public static $DBName;
    public static $DBUser;
    public static $DBPass;

    /** @var \PDO */
    protected static $dbh;


    /**
     * @param string $host
     * @param string $dbName
     * @param string $dbUser
     * @param string $dbPass
     *
     * @throws \Api\v1\Exception
     */
    public function __construct($host = '', $dbName = '', $dbUser = '', $dbPass = '')
    {
        try {
            self::$dbh = new \PDO('mysql:host=' . self::$DBHost . ';dbname=' . self::$DBName . ';charset=utf8', self::$DBUser, self::$DBPass);
            self::$dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $this;
        }
        catch (\PDOException $e) {
            //print(json_encode($e));
            throw new Exception('Cannot connect to the database.', 500);
        }
    }


    /**
     * @param string $sql
     * @param array  $params
     * @param array  $sort
     * @param array  $limit
     *
     * @throws \Api\v1\Exception
     *
     * @return array
     */
    public function find($sql, $params = [], $sort = [], $limit = [])
    {
        if (empty($sql)) {
            throw new Exception('Empty query');
        }

        $ORDERBY = (empty($sort[0]) || empty($sort[1])) ? '' : ' ORDER BY `' . $sort[0] . '` ' . $sort[1];
        $LIMIT = (empty($limit) || empty($limit[0])) ? '' :
            ' LIMIT ' . $limit[0] . ((!isset($limit[1])) ? '' : ', ' . $limit[1]);

        $stmt = self::$dbh->prepare($sql . $ORDERBY . $LIMIT);
        $stmt->execute($params);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }


    public function findOne($sql, $params = [], $sort = [])
    {
        if (empty($sql)) {
            throw new Exception('Empty query');
        }

        $findOne = self::find($sql, $params, $sort, [1]);

        if ($findOne) {
            return $findOne[0];
        }
        else {
            return [];
        }
    }


    /**
     * @param $tableName
     * @param $values
     *
     * @throws \Api\v1\Exception
     *
     * @return int
     */
    public function insert($tableName, $values)
    {
        if (empty($tableName) || empty($values)) {
            throw new Exception('Empty query');
        }

        $sql = "INSERT" . " INTO `" . $tableName . "`
            (" . implode(",", array_keys($values)) . ")
            VALUES (:" . implode(",:", array_keys($values)) . ")";

        $stmt = self::$dbh->prepare($sql);
        $stmt->execute($values);

        return intval(self::$dbh->lastInsertId());
    }


    /**
     * @param       $sql
     * @param array $params
     * @param array $limit
     *
     * @return int The number of affected rows
     * @throws \Api\v1\Exception\Validation
     */
    public function update($sql, $params = [], $limit = [1])
    {
        if (empty($sql)) {
            throw new Validation('Empty query');
        }

        $LIMIT = (empty($limit) || empty($limit[0])) ? '' :
            ' LIMIT ' . $limit[0] . ((!isset($limit[1])) ? '' : ', ' . $limit[1]);

        $stmt = self::$dbh->prepare($sql . $LIMIT);
        $stmt->execute($params);

        return $stmt->rowCount();
    }


    public function upsert($tableName, $find = [], $update = [])
    {
        if (empty($tableName) || empty($find)) {
            throw new Validation('Empty query');
        }

        $find = array_merge($find, $update);

        $onUpdate = [];
        foreach ($update as $k => $v) {
            $onUpdate[] = $k . "=VALUES(" . $k . ")";
        }

        $sql = "INSERT" . " INTO `" . $tableName . "`
            (" . implode(",", array_keys($find)) . ")
            VALUES (:" . implode(",:", array_keys($find)) . ")

            ON DUPLICATE KEY
                UPDATE " . implode(',', $onUpdate);

        $stmt = self::$dbh->prepare($sql);
        $stmt->execute($find);

        return self::$dbh->lastInsertId();
    }


    /**
     * @param     $tableName
     * @param     $where
     * @param     $params
     * @param int $limit
     *
     * @return int The number of affected rows
     * @throws \Api\v1\Exception\Validation
     */
    public function remove($tableName, $where, $params, $limit = 1)
    {
        if (empty($tableName) || empty($where)) {
            throw new Validation('Empty query');
        }

        $LIMIT = !empty($limit) ? ' LIMIT ' . $limit : '';

        $sql = "DELETE" . " FROM `" . $tableName . "`
            WHERE " . $where . $LIMIT;

        $stmt = self::$dbh->prepare($sql);
        $stmt->execute($params);

        return $stmt->rowCount();
    }
}
