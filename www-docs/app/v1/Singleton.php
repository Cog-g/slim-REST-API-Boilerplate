<?php
namespace API\v1;

abstract class Singleton
{
    protected static $instances = [];


    protected function __construct() { }


    private function __clone() { }


    private function __wakeup() { }


    /**
     * @return static
     */
    public static function getInstance()
    {
        /** @var $instances static */
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new $class();
        }

        return self::$instances[$class];
    }
}
