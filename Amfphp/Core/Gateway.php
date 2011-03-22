<?php
/*
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */


/**
 * where everything comes together in amfphp.
 * The class used for the entry point of a remoting call
 * TODO consider moving everything from context directly to gateway class. A.S.
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Gateway {

    /**
     * hook called when the serialized request comes in.  Anything the callee returns is ignored.
     * @param String $rawData the raw http data
     */
    const FILTER_REQUEST_SERIALIZED = "FILTER_REQUEST_SERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf deserializer.
     * Plugin should return a Amfphp_Core_Common_IDeserializer if it recognizes the content type
     * @param Amfphp_Core_Common_IDeserializer $deserializer the deserializer. null at call in gateway.
     * @param String $contentType
     */
    const FILTER_GET_DESERIALIZER = "FILTER_GET_DESERIALIZER";
    
    /**
     * hook called after the request is deserialized. The callee can modify the data and return it.
     * @param mixed $deserializedRequest
     */
    const FILTER_REQUEST_DESERIALIZED = "FILTER_REQUEST_DESERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf deserialized request handler. 
     * Plugin should return a Amfphp_Core_Common_IDeserializedRequestHandler if it recognizes the request
     * @param Amfphp_Core_Common_IDeserializedRequestHandler $deserializedRequestHandler null at call in gateway.
     * @param String $contentType
     */
    const FILTER_GET_DESERIALIZED_REQUEST_HANDLER = "FILTER_GET_DESERIALIZED_REQUEST_HANDLER";

    /**
     * hook called when the response is ready but not yet serialized.  The callee can modify the data and return it.
     * @param $deserializedResponse
     */
    const FILTER_RESPONSE_DESERIALIZED = "FILTER_RESPONSE_DESERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf exception handler.
     * If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Common_IExceptionHandler
     * @param Amfphp_Core_Common_IExceptionHandler $exceptionHandler. null at call in gateway.
     * @param String $contentType
     */
    const FILTER_GET_EXCEPTION_HANDLER = "FILTER_GET_EXCEPTION_HANDLER";

    /**
     * hook called to allow a plugin to override the default amf serializer.
     * @param Amfphp_Core_Common_ISerializer $serializer the serializer. null at call in gateway.
     * @param String $contentType
     * Plugin sets to a Amfphp_Core_Common_ISerializer if it recognizes the content type
     */
    const FILTER_GET_SERIALIZER = "FILTER_GET_SERIALIZER";

    /**
     * hook called when the packet response is ready and serialized. Anything the callee returns is ignored.
     * @param String $rawData the raw http data
     */
    const FILTER_RESPONSE_SERIALIZED = "FILTER_RESPONSE_SERIALIZED";


    /**
     * config.
     * @var Amfphp_Core_Config
     */
    private $config;

    /**
     * typically the $_GET array.
     * @var array
     */
    private $getData;

    /**
     * typically the $_POST array.
     * @var array
     */
    private $postData;

    /**
     * the serialized request 
     * @var String 
     */
    private $rawInputData;

    /**
     * the content type. For example for amf, application/x-amf
     * @var String
     */
    private $contentType;
    /**
     *
     */
    /**
     * constructor
     * @param array $getData typically the $_GET array.
     * @param array $postData typically the $_POST array.
     * @param String $rawInputData
     * @param String $contentType
     * @param Amfphp_Core_Config $config optional. The default config object will be used if null
     */
    public function  __construct(array $getData, array $postData, $rawInputData, $contentType, Amfphp_Core_Config $config = null) {
        $this->getData = $getData;
        $this->postData = $postData;
        $this->rawInputData = $rawInputData;
        $this->contentType = $contentType;
        if($config){
            $this->config = $config;
        }else{
            $this->config = new Amfphp_Core_Config();
        }

    }
    
    /**
     * The service method runs the gateway application.  It deserializes the raw data passed into the constructor as an Amfphp_Core_Amf_Packet, handles the headers,
     * handles the messages as requests to services, and returns the responses from the services
     * It does not however handle output headers, gzip compression, etc. that is the job of the calling script
     *
     * @return <String> the serialized amf packet containg the service responses
     */
    public function service(){
        $hookManager = Amfphp_Core_FilterManager::getInstance();
        $defaultHandler = new Amfphp_Core_Amf_Handler();
        $deserializedResponse = null;
        try{
            Amfphp_Core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolder, $this->config->pluginsConfig, $this->config->disabledPlugins);
            //call hook for filtering serialized incoming packet
            $this->rawInputData = $hookManager->callFilters(self::FILTER_REQUEST_SERIALIZED, $this->rawInputData);

            //call hook to get the deserializer
            $deserializer = $hookManager->callFilters(self::FILTER_GET_DESERIALIZER, $defaultHandler, $this->contentType);
            
            //deserialize
            $deserializedRequest = $deserializer->deserialize($this->getData, $this->postData, $this->rawInputData);

            //call hook for filtering deserialized request
            $deserializedRequest = $hookManager->callFilters(self::FILTER_REQUEST_DESERIALIZED, $deserializedRequest);

            //create service router
            $serviceRouter = new Amfphp_Core_Common_ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);

            //call hook to get the deserialized request handler
            $deserializedRequestHandler = $hookManager->callFilters(self::FILTER_GET_DESERIALIZED_REQUEST_HANDLER, $defaultHandler, $this->contentType);

            //handle request
            $deserializedResponse = $deserializedRequestHandler->handleDeserializedRequest($deserializedRequest, $serviceRouter);

            //call hook for filtering the deserialized response
            $deserializedResponse = $hookManager->callFilters(self::FILTER_RESPONSE_DESERIALIZED, $deserializedResponse);

        }catch(Exception $exception){
            //call hook to get the exception handler
            $exceptionHandler = $hookManager->callFilters(self::FILTER_GET_EXCEPTION_HANDLER, $defaultHandler, $this->contentType);

            //handle exception
            $deserializedResponse = $exceptionHandler->handleException($exception);

        }

        //call hook to get the serializer
        $serializer = $hookManager->callFilters(self::FILTER_GET_SERIALIZER, $defaultHandler, $this->contentType);

        //serialize
        $rawOutputData = $serializer->serialize($deserializedResponse);
        
        //call hook for filtering the serialized response packet
        $rawOutputData = $hookManager->callFilters(self::FILTER_RESPONSE_SERIALIZED, $rawOutputData);

        return $rawOutputData;

    }

    /**
     * get the response headers. This might be expanded for stuff like gzip, etc. For now just a content type
     * @return array
     */
    public function getResponseHeaders(){
        return array("Content-type : " . $this->contentType);
    }


}
?>
