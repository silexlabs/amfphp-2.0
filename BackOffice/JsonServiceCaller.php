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
 * Makes a call to the amfphp entry point and returns the data. unused at the moment, but useful later hopefully
 * 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice
 */
class Amfphp_BackOffice_JsonServiceCaller {

    /**
     * path to amfphp entry point
     * @var string
     */
    protected $amfphpEntryPointUrl;

    /**
     * constructor
     * @param string $amfphpEntryPointUrl
     */
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
    function call($serviceName, $methodName, $parameters = array()) {
        if (!in_array('curl', get_loaded_extensions())) {
            $error = 'curl php extension unavailable. Can not make call. This does not mean yœu can not use amfPHP, however it does mean that most of the functionality in the Back Office will not work. This must be changed by your Hosting provider.';
            echo $error;
            throw new Exception($error);
        }
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
        if ($response == 'null') {
            return null;
        }
        $decoded = json_decode($response);
        if ($decoded == null) {
            throw new Exception("could not decode response : $response");
        }

        return $decoded;
    }

}

?>
