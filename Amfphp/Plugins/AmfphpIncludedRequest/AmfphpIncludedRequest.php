<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_AmfphpIncludedRequest
 */

/**
 * allows inclusion of an amfPHP entry point script. This is so that a script running on the same server can 
 * include the entry point script and execute a request.
 * 
 * It works by setting the required globals describing the request, including the entry point script, and retrieving the response data.
 * 
 * globals are:
 * $amfphpIncludedRequestServiceName
 * $amfphpIncludedRequestMethodName
 * $amfphpIncludedRequestParameters
 * $amfphpIncludedRequestReturnValue
 * 
 * declare and them before including your entry point script. For example:
 * 
 * $amfphpIncludedRequestServiceName = "AmfphpDiscoveryService";
 * $amfphpIncludedRequestMethodName = "discover";
 * $amfphpIncludedRequestParameters = array();
 * $amfphpIncludedRequestReturnValue = null;
 * require(dirname(__FILE__) . '/../../Amfphp/index.php');
 * print_r($amfphpIncludedRequestReturnValue);
 * 
 * the plugin considers that if $amfphpIncludedRequestServiceName is set, then the request is an included request and that it must be handled here.
 *
 * @package Amfphp_Plugins_AmfphpIncludedRequest
 * @author Ariel Sommeria-Klein
 */
class AmfphpIncludedRequest implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null) {

        global $amfphpIncludedRequestServiceName;
        if (!isset($amfphpIncludedRequestServiceName)) {
            return;
        }
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, "filterHandler");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, "filterHeaders");
    }

    /**
     * if no content type, then returns this.
     * @param mixed null at call in gateway.
     * @param String $contentType
     * @return this or null
     */
    public function filterHandler($handler, $contentType) {
        global $amfphpIncludedRequestServiceName;
        if (isset($amfphpIncludedRequestServiceName)) {
            return $this;
        }
    }

    /**
     * deserialize
     * @see Amfphp_Core_Common_IDeserializer
     * @param array $getData
     * @param array $postData
     * @param string $rawPostData
     * @return string
     */
    public function deserialize(array $getData, array $postData, $rawPostData) {
        global $amfphpIncludedRequestServiceName;
        global $amfphpIncludedRequestMethodName;
        global $amfphpIncludedRequestParameters;
        return (object) array("serviceName" => $amfphpIncludedRequestServiceName, "methodName" => $amfphpIncludedRequestMethodName, "parameters" => $amfphpIncludedRequestParameters);
    }

    /**
     * call service
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     * @param array $deserializedRequest
     * @param Amfphp_Core_Common_ServiceRouter $serviceRouter
     * @return the service call response
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter) {
        return $serviceRouter->executeServiceCall($deserializedRequest->serviceName, $deserializedRequest->methodName, $deserializedRequest->parameters);
    }

    /**
     * handle exception
     * @see Amfphp_Core_Common_IExceptionHandler
     * @param Exception $exception
     * @return Exception
     */
    public function handleException(Exception $exception) {
        return $exception;
    }

    /**
     * set the return data in global $amfphpIncludedRequestReturnValue
     * @see Amfphp_Core_Common_ISerializer
     * @param mixed $data
     * @return string the encoded JSON string sent to JavaScript
     */
    public function serialize($data) {
        global $amfphpIncludedRequestReturnValue;

        $amfphpIncludedRequestReturnValue = $data;
        //typically an entry point includes a $gateway->output(); call. return null so that nothing ends up on the output
        return null;
    }

    /**
     * filter the headers to make sure the content type is set to text/html if the request was handled by the service browser
     * @param array $headers
     * @param string $contentType
     * @return array
     */
    public function filterHeaders($headers, $contentType) {
        if (isset($amfphpIncludedRequestServiceName)) {
            return array();
        }
    }

}

?>
