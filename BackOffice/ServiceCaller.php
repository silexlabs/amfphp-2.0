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
class Amfphp_BackOffice_ServiceCaller {

    protected $amfphpEntryPointUrl;

    public function __construct($amfphpEntryPointUrl) {
        $this->amfphpEntryPointUrl = $amfphpEntryPointUrl;
        
        
    }

    /**
     * makes a request to the amfphp server
     * @param string $serviceName
     * @param string $methodName
     * @param string $parameters
     * @return mixed array or object, json decoded
     */
    function makeAmfphpJsonServiceCall($serviceName, $methodName, $parameters = array()) {
        $jsonEncodedParams = json_encode($parameters);
        $requestString = "{\"serviceName\":\"$serviceName\", \"methodName\":\"$methodName\", \"parameters\":$jsonEncodedParams}";
        //echo $requestString;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->amfphpEntryPointUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
        curl_setopt($curl, CURLOPT_POST, 1);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $requestString);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, false);
        $response = curl_exec($curl);
        $decoded = json_decode($response);
        //echo $response;
        if(!$decoded){
            throw new Exception("service called failed. server response: \n $response \nrequest: \n \n$requestString" );
        }
        return $decoded;
        
    }

}

?>
