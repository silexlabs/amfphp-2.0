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
     * '../Amfphp/index.php'
     * '../Examples/Php/index.php'
     * '../Tests/TestData/index.php'
     * @var String 
     */
    public $amfphpEntryPointPath = '../Amfphp/index.php';

    /**
     * set credentials for back office here.
     * expected format: username => password
     * See constructor for example code.
     * 
     * @var array 
     */
    public $backOfficeCredentials;

    /**
     * set to false for private server. Set to true for public server.
     * true by default
     * @var boolean
     */
    public $requireSignIn = true;

    /**
     * constructor
     */
    public function __construct() {
        $this->backOfficeCredentials = array();
        //example code for username + password:
        //$this->backOfficeCredentials['your Username'] = 'your Password';
    }

    /**
     * determine url to amfphp. If in config it contains 'http', we consider it's absolute. Otherwise it's relative, and we build it.
     * @return string
     */
    public function resolveAmfphpEntryPointUrl() {
        $httpMarkerPos = strpos($this->amfphpEntryPointPath, 'http');
        if ($httpMarkerPos === false) {
            $scriptUrl = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
            //remove everything after last '/'
            $scriptUrlPath = substr($scriptUrl, 0, strrpos($scriptUrl, '/'));
            return $scriptUrlPath . '/' . $this->amfphpEntryPointPath;
        } else {
            return $this->amfphpEntryPointPath;
        }
    }

}

?>
