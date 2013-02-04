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
 * This is a test/example service. Remove it for production use
 *
 * @package Amfphp_Services
 * @author Ariel Sommeria-klein
 */
class ExampleService {

    /**
     * return the same data as what was sent
     * @param mixed $param
     * @return mixed
     */
    public function returnOneParam($param){
        return $param;
    }


}
?>
