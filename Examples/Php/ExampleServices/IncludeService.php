<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples_ExampleService
 */

require_once dirname(__FILE__) . '/../Includes/Included.php';
/**
 * an example service to show how best include another PHP file
 * @package Amfphp_Examples_ExampleService
 * @author Ariel Sommeria-klein
 */
class IncludeService {
    
    
    public function returnString(){
        return "Included";
    }
}

?>
