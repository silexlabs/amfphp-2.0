<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Json
 */

/**
 * Enables amfPHP to receive and reply with JSON
 * This plugin can be deactivated if the project doesn't need to support JSON
 * strings and returned as JSON strings using POST parameters.
 *
 * You must add the 'application/json' content type, or set it in the headers so that it is recognized as a call to be handled by this plugin.
 * for example:
 * http://yourserver.com/Amfphp/?contentType=application/json
 *
 * Here is some sample code using Javascript with JQuery:
 * <code>
 * var callDataObj = {"serviceName":"PizzaService", "methodName":"getPizza","parameters":[]};
 * var callData = JSON.stringify(callDataObj);
 * $.post("http://yourserver.com/Amfphp/?contentType=application/json", callData, onSuccess);
 * </code>
 *
 * Requires at least PHP 5.2.
 *
 * @package Amfphp_Plugins_Json
 * @author Yannick DOMINGUEZ
 */
class AmfphpJson implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer
{
    /**
    * the content-type string indicating a JSON content
    */
    const JSON_CONTENT_TYPE = 'application/json';

    protected $returnErrorDetails = false;

    /**
     * constructor. Add filters on the HookManager.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null)
    {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST_HANDLER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_EXCEPTION_HANDLER, $this, 'filterHandler');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZER, $this, 'filterHandler');
        $this->returnErrorDetails = (isset ($config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]) && $config[Amfphp_Core_Config::CONFIG_RETURN_ERROR_DETAILS]);

    }

    /**
     * If the content type contains the 'json' string, returns this plugin
     * @param mixed null at call in gateway.
     * @param  String $contentType
     * @return this   or null
     */
    public function filterHandler($handler, $contentType)
    {
        if (strpos($contentType, self::JSON_CONTENT_TYPE) !== false) {
            return $this;
        }
    }

    public function deserialize(array $getData, array $postData, $rawPostData)
    {
        $jsonString = '';
        if ($rawPostData != '') {
            $jsonString = $rawPostData;
        } elseif (isset($postData['json'])) {
            $jsonString = $postData['json'];
        } else {
            throw new Exception('json call data not found. It must be sent in the post data');
        }

        $deserializedRequest = json_decode($rawPostData);
        if (!isset ($deserializedRequest->serviceName)) {
            throw new Exception("<pre>Service name field missing in JSON. \njsonString:\n $jsonString \ndecoded: \n" . print_r($deserializedRequest, true) . '</pre>');
        }
        if (!isset ($deserializedRequest->methodName)) {
            throw new Exception("<pre>MethodName field missing in JSON. \njsonString:\n $jsonString \ndecoded: \n" . print_r($deserializedRequest, true) . '</pre>');
        }

        return $deserializedRequest;

    }


    /**
     * Retrieve the serviceName, methodName and parameters from the PHP object
     * representing the JSON string
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     * @return the service call response
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter)
    {
        $serviceName = $deserializedRequest->serviceName;
        $methodName = $deserializedRequest->methodName;

        $parameters = array();
        if (isset ($deserializedRequest->parameters)) {
            $parameters = $deserializedRequest->parameters;
        }

        return $serviceRouter->executeServiceCall($serviceName, $methodName, $parameters);

    }

    /**
     * don't format; just throw! In this way ajax libs will have their error functions triggered
     * @see Amfphp_Core_Common_IExceptionHandler
     */
    public function handleException(Exception $exception)
    {
        throw $exception;

    }

    /**
     * Encode the PHP object returned from the service call into a JSON string
     * @see Amfphp_Core_Common_ISerializer
     * @return the encoded JSON string sent to JavaScript
     */
    public function serialize($data)
    {
        return json_encode($data);

    }

}
