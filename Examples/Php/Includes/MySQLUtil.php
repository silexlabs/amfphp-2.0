<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples_Authentication
 */

/**
 * MySql Util
 * @package Amfphp_Examples_Authentication
 * @author Sven Dens
 */
class MySQLUtil {
    /**
     * get connection
     * @return PDO
     */
    public static function getConnection() {

        return new PDO('mysql:host=' . MYSQL_HOST . ';dbname=' . MYSQL_DB,
                        MYSQL_USER,
                        MYSQL_PASS,
                        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
        );
    }

    /**
     * transactions available
     * @return boolean
     */
    public static function transactionsAvailable() {
        $pdo = MySQLUtil::getConnection();
        $tsql = "SELECT ENGINE";
        $tsql .= " FROM information_schema.TABLES";
        $tsql .= " WHERE TABLE_SCHEMA = :schema";

        $stmt = $pdo->prepare($tsql);
        $db = MYSQL_DB;
        $stmt->bindParam(':schema', $db, PDO::PARAM_STR);
        $stmt->execute();

        $answer = true;
        while ($result = $stmt->fetch(PDO::FETCH_OBJ)) {
            if (strtolower($result->ENGINE) != "innodb") {
                $answer = false;
                break;
            }
        }

        return $answer;
    }

}
/**
 * Db Util
 * @package Amfphp_Examples_Authentication
 * @author Sven Dens
 */
class DBUtils {
    /**
     * pdo available?
     * @return boolean
     */
    public static function pdoAvailable() {
        $answer = true;
        if (!defined('PDO::ATTR_DRIVER_NAME')) {
            $answer = false;
        }
        return $answer;
    }
    /**
     *  quick hash, with a simple method to work without a salt. A salt is preferred of course, but this keeps things simple
     * @param string $password
     * @return string
     */
    public static function hashPassword($password) {
        $salt = sha1(md5($password));
        $password = md5($password . $salt);

        return $password;
    }

}