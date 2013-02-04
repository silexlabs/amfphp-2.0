<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Services
 */

/**
 * This is a test/example service. 
 *
 * @package Amfphp_Services
 * @author Ariel Sommeria-klein
 */
class TestService {

    /**
     * return one param
     * @param mixed $param
     * @return mixed 
     */
    public function returnOneParam($param) {
        return $param;
    }

    /**
     * return sum
     * @param int $number1
     * @param int $number2
     * @return int 
     */
    public function returnSum($number1, $number2) {
        return $number1 + $number2;
    }

    /**
     * return null
     * @return null 
     */
    public function returnNull() {
        return null;
    }

    /**
     * return bla
     * @return String 
     */
    public function returnBla() {
        return 'bla';
    }

    /**
     * throy exception
     * @param string $arg1
     * @throws Exception
     */
    public function throwException($arg1) {
        throw new Exception("test exception $arg1", 123);
    }

    /**
     * return after one second
     * @return String 
     */
    public function returnAfterOneSecond() {
        sleep(1);
        return 'slept for 1 second';
    }

    /**
     * return test header
     * @return mixed
     */
    public function returnTestHeader() {
        $header = Amfphp_Core_Amf_Handler::$requestPacket->headers[0];
        return $header->data;
    }

    /**
     * shouldn't appear in the service browser or be available as a service
     */
    public function _reservedMethod() {
        
    }

    /**
     * return array
     * @return array 
     */
    public function returnArray() {
        return array(0, 1 => 2, 3 => 4, 5 => array(6 => 7));
    }

    /**
     * return opposite
     * @param boolean $value
     * @return boolean 
     */
    public function returnOpposite($value) {
        return !$value;
    }

    /**
     * return bitwise and
     * @param boolean $value1
     * @param boolean $value2
     * @return boolean 
     */
    public function returnBitwiseAnd($value1, $value2) {
        return ($value1 && $value2);
    }

    /**
     * static return one param
     * @param mixed $param
     * @return mixed
     */
    public static function staticReturnOneParam($param) {
        return $param;
    }

}

?>
