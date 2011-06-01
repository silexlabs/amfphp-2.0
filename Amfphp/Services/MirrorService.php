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
 * MirrorService is a test/example service. Remove it for production use
 *
 * @package Amfphp_Services
 * @author Ariel Sommeria-klein
 */
class MirrorService {

    public function returnOneParam($param){
        return $param;
    }

    public function returnSum($number1, $number2){
        return $number1 + $number2;
    }

    public function returnNull(){
        return null;
    }

    public function returnBla(){
        return "bla";
    }


}
?>
