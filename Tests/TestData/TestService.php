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
     *
     * @param mixed $param
     * @return mixed 
     */
    public function returnOneParam($param){
        return $param;
    }
    
    /**
     *
     * @param int $number1
     * @param int $number2
     * @return int 
     */
    public function returnSum($number1, $number2){
        return $number1 + $number2;
    }
    
    /**
     *
     * @return null 
     */
    public function returnNull(){
        return null;
    }
    
    /**
     *
     * @return String 
     */
    public function returnBla(){
        return 'bla';
    }
    
    public function throwException($arg1){
        throw new Exception("test exception $arg1", 123);
    }
    
    /**
     *
     * @return String 
     */
    public function returnAfterOneSecond(){
        sleep(1);
        return 'slept for 1 second';
    }
    
    public function returnTestHeader(){
        $header = Amfphp_Core_Amf_Handler::$requestPacket->headers[0];
        return $header->data;
    }
    
    /**
     * shouldn't appear in the service browser or be available as a service
     */
    public function _reservedMethod(){
        
    }
    
    /**
     *
     * @return array 
     */
    public function returnArray(){
        return array(0, 1 =>2, 3=> 4, 5 => array(6 => 7));
    }


}
?>
