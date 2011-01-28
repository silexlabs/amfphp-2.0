<?php

/**
 * where everything comes together in amfphp.
 * The class used for the entry point of a remoting call
 * TODO consider moving everything from context directly to gateway class. A.S.
 *
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Gateway {

    /**
     * hook called when the serialized request comes in.  Anything the callee returns is ignored.
     * @param String $rawData the raw http data
     */
    const HOOK_REQUEST_SERIALIZED = "HOOK_REQUEST_SERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf deserializer.
     * Plugin should return a Amfphp_Core_Common_IDeserializer if it recognizes the content type
     * @param String $contentType
     * @param Amfphp_Core_Common_IDeserializer $deserializer the deserializer. null at call in gateway.
     * @return Amfphp_Core_Common_IDeserializer
     */
    const HOOK_GET_DESERIALIZER = "HOOK_GET_DESERIALIZER";
    
    /**
     * hook called after the request is deserialized. The callee can modify the data and return it.
     * @param mixed $deserializedRequest
     * @return mixed the modified data
     */
    const HOOK_REQUEST_DESERIALIZED = "HOOK_REQUEST_DESERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf deserialized request handler. 
     * Plugin should return a Amfphp_Core_Common_IDeserializedRequestHandler if it recognizes the request
     * @param String $contentType
     * @param Amfphp_Core_Common_IDeserializedRequestHandler $deserializedRequestHandler null at call in gateway.
     * @return Amfphp_Core_Common_IDeserializedRequestHandler
     */
    const HOOK_GET_DESERIALIZED_REQUEST_HANDLER = "HOOK_GET_DESERIALIZED_REQUEST_HANDLER";

    /**
     * hook called when the response is ready but not yet serialized.  The callee can modify the data and return it.
     * @param Amfphp_Core_Amf_Packet $responsePacket the deserialized packet
     * @return mixed the modified data
     */
    const HOOK_RESPONSE_DESERIALIZED = "HOOK_RESPONSE_DESERIALIZED";

    /**
     * hook called to allow a plugin to override the default amf exception handler.
     * If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Common_IExceptionHandler
     * @param String $contentType
     * @param Amfphp_Core_Common_IExceptionHandler $exceptionHandler. null at call in gateway.
     * @return Amfphp_Core_Common_IExceptionHandler
     */
    const HOOK_GET_EXCEPTION_HANDLER = "HOOK_GET_EXCEPTION_HANDLER";

    /**
     * hook called to allow a plugin to override the default amf serializer.
     * @param String $contentType
     * @param Amfphp_Core_Common_ISerializer $serializer the serializer. null at call in gateway.
     * Plugin sets to a Amfphp_Core_Common_ISerializer if it recognizes the content type
     */
    const HOOK_GET_SERIALIZER = "HOOK_GET_SERIALIZER";

    /**
     * hook called when the packet response is ready and serialized. Anything the callee returns is ignored.
     * @param String $rawData the raw http data
     */
    const HOOK_RESPONSE_SERIALIZED = "HOOK_RESPONSE_SERIALIZED";


    /**
     * config.
     * @var Amfphp_Core_Config
     */
    private $config;

    /**
     * the serialized request 
     * @var String 
     */
    private $rawInputData;

    /**
     * any eventual GET parameters
     */
    private $getData;

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
     * @param String $rawInputData
     * @param array $getData typically the $_GET array. 
     * @param String $contentType
     * @param Amfphp_Core_Config $config optional. The default config object will be used if null
     */
    public function  __construct($rawInputData, array $getData, $contentType, Amfphp_Core_Config $config = null) {
        $this->rawInputData = $rawInputData;
        $this->getData = $getData;
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
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $defaultHandler = new Amfphp_Core_Amf_Handler();
        $deserializedResponse = null;
        try{
            Amfphp_Core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolder, $this->config->pluginsConfig, $this->config->disabledPlugins);
            //call hook for reading serialized incoming packet
            $hookManager->callHooks(self::HOOK_REQUEST_SERIALIZED, array($this->rawInputData));

            //call hook for overriding the default deserializer
            $fromHooks = $hookManager->callHooks(self::HOOK_GET_DESERIALIZER, array($this->contentType, null));
            $deserializer = null;
            if($fromHooks && isset ($fromHooks[1])){
                $deserializer = $fromHooks[1];
            }else{
                $deserializer = $defaultHandler;
            }
            
            //deserialize
            $deserializedRequest = $deserializer->deserialize($this->rawInputData, $this->getData);

            //call hook for reading/modifying deserialized request
            $fromHooks = $hookManager->callHooks(self::HOOK_REQUEST_DESERIALIZED, array($deserializedRequest));
            if($fromHooks && isset ($fromHooks[0])){
                $deserializedRequest = $fromHooks[0];
            }

            //create service router
            $serviceRouter = new Amfphp_Core_Common_ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);

            //call hook for overriding the default deserialized request handler
            $fromHooks = $hookManager->callHooks(self::HOOK_GET_DESERIALIZED_REQUEST_HANDLER, array($this->contentType, null));
            $deserializedRequestHandler = null;
            if($fromHooks && isset ($fromHooks[1])){
                $deserializedRequestHandler = $fromHooks[1];
            }else{
                $deserializedRequestHandler = $defaultHandler;
            }

            //handle request
            $deserializedResponse = $deserializedRequestHandler->handleDeserializedRequest($deserializedRequest, $serviceRouter);

            //call hook for reading/modifying deserialized response
            $fromHooks = $hookManager->callHooks(self::HOOK_RESPONSE_DESERIALIZED, array($deserializedResponse));
            if($fromHooks && isset ($fromHooks[0])){
                $deserializedResponse = $fromHooks[0];
            }

        }catch(Exception $exception){
            //call hook for overriding the default exception handler
            $fromHooks = $hookManager->callHooks(self::HOOK_GET_EXCEPTION_HANDLER, array($this->contentType, null));
            $exceptionHandler = null;
            if($fromHooks && isset ($fromHooks[1])){
                $exceptionHandler = $fromHooks[1];
            }else{
                $exceptionHandler = $defaultHandler;
            }

            //handle exception
            $deserializedResponse = $exceptionHandler->handleException($exception);

        }

        //call hook for overriding the default serializer
        $fromHooks = $hookManager->callHooks(self::HOOK_GET_SERIALIZER, array($this->contentType, null));
        $serializer = null;
        if($fromHooks && isset ($fromHooks[1])){
            $serializer = $fromHooks[1];
        }else{
            $serializer = $defaultHandler;
        }

        $rawOutputData = $serializer->serialize($deserializedResponse);
        
        //call hook for reading serialized response packet
        $hookManager->callHooks(self::HOOK_RESPONSE_SERIALIZED, array($rawOutputData));

        return $rawOutputData;

    }


}
?>
