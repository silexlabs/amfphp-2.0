<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice
 * 
 */

/**
 * Makes a call to the amfphp entry point and returns the data
 * 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice
 */
class Amfphp_BackOffice_IncludeServiceCaller {

    /**
     * path to amfphp entry point
     * @var string
     */
    protected $amfphpEntryPointPath;

    /**
     * constructor
     * @param string $amfphpEntryPointPath
     */
    public function __construct($amfphpEntryPointPath) {
        $this->amfphpEntryPointPath = $amfphpEntryPointPath;
    }

    /**
     * makes a request to the amfphp server
     * @param string $serviceName
     * @param string $methodName
     * @param string $parameters
     * @return mixed whtever the service method returns. If it's an exception it's thrown again.
     */
    function call($serviceName, $methodName, $parameters = array()) {
        global $amfphpIncludedRequestServiceName;
        $amfphpIncludedRequestServiceName = $serviceName;
        global $amfphpIncludedRequestMethodName;
        $amfphpIncludedRequestMethodName = $methodName;
        global $amfphpIncludedRequestParameters;
        $amfphpIncludedRequestParameters = $parameters;
        global $amfphpIncludedRequestReturnValue;
        require($this->amfphpEntryPointPath);
        return $amfphpIncludedRequestReturnValue;

    }

}

?>
