<?php


/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_BackOffice
 */

/**
 * config for the backoffice
 * 
 * @package Amfphp_BackOffice
 * @author Ariel Sommeria-Klein
 */
class Amfphp_BackOffice_Config {
    /**
     * path to amfPHP. relative or absolute. If relative, be careful, it's relative to the script, not this file.
     * 'http://silexlabs.org/Tests/TestData/';
     * some entry points that pertain to the code:
     * '../Amfphp/'
     * '../Examples/Php/'
     * '../Tests/TestData/'
     * @var String 
     */
    public $amfphpEntryPointUrl = '../Amfphp/';
    
    public function resolveAmfphpEntryPointUrl(){
//determine url to amfphp. If in config it contains 'http', we consider it's absolute. Otherwise it's relative, and we build it.
        $httpMarkerPos = strpos($this->amfphpEntryPointUrl, 'http');
        if ($httpMarkerPos === false) {
            $scriptUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
            //remove everything after last '/'
            $scriptUrlPath = substr($scriptUrl, 0, strrpos($scriptUrl, '/'));
            return $scriptUrlPath . '/' . $this->amfphpEntryPointUrl;
        }else{
            return $this->amfphpEntryPointUrl;
        }
    }
}

?>
