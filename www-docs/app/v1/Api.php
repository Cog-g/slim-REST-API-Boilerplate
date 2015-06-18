<?php
namespace API\v1;

use API\v1\Exception;

abstract class Api
{

    const tokenValidity  = 1200; // 20 minutes
    const tokenSecretKey = "®ÂìÉ32ÅjªôYNißàR]/Ìù{¨hZ¾ý0õÓD¿º·)_Ul±°%`,ØÊþm÷oÕËÎ´ð¢â.^¡X¦¸ÁK";

    public static $params = [];
    // Add any other variable you may need, here.

    public static $debug = [];
    public static $isDebug = 0;


    // Token stuff
    // ==============================================================

    /**
     * @param int    $userID
     * @param string $time
     *
     * @return string
     */
    public static function craftToken($userID, $time)
    {
        return md5($userID . self::tokenSecretKey . $time);
    }


    /**
     * Check if a token exists in the database and is
     * not expired.
     *
     * @param int    $userID
     * @param int    $time
     * @param string $token
     *
     * @return bool
     * @throws \API\v1\Exception\Forbidden
     * @throws \API\v1\Exception\Validation
     */
    public static function validateToken($userID, $time, $token)
    {
        if (empty($userID) || empty($time) || empty($token)) {
            throw new Exception\Validation('Authorization data is missing');
        }

        // Check if the token is valid
        if ($token !== self::craftToken($userID, $time)) {
            throw new Exception\Forbidden('Token is not valid');
        }

        // Check if the token exists in the database
        $db = new DB();
        if (!$isTokenInTheDB = $db->findOne("SELECT Updated, Role
            FROM user_sessions
            WHERE Token = :Token
            AND UserID = :UserID
            AND Created = :Created", [
            'Token' => $token,
            'UserID' => $userID,
            'Created' => gmdate('c', $time)
        ])
        ) {
            throw new Exception\Forbidden('Token does not exist');
        }

        // The token exists
        // Check if the token is still valid
        if (strtotime($isTokenInTheDB['Updated'] . '+00:00') <= (time() - self::tokenValidity)) {
            throw new Exception\Forbidden('Token expired');
        }


        // Everything is ok, let's go
        // ===================================================================

        User::$ID = intval($userID);
        User::$token = $token;
        User::$session = $time;


        return true;
    }

}
