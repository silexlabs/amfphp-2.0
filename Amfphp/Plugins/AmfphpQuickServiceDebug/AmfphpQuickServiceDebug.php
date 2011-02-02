<?php
/**
 * simple (and limited) plugin to debug services.
 * call the gateway with the following GET parameters:
 * serviceName: the service name
 * methodName : the method to call on the service
 * parameters: the parameters of the call, separated by commas. These are not parsed as yet, so they are passed as strings
 * the plugin will return a print_r of the service call's return value if all goes well,
 * and some info about the exception if things mess up.
 *
 * @author Ariel Sommeria-Klein
 */
class AmfphpQuickServiceDebug implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer {

    /**
     * indicate the service name in this field in the GET parameters
     */
    const FIELD_SERVICE_NAME = "serviceName";

    /**
     * indicate the method name in this field in the GET parameters
     */
    const FIELD_METHOD_NAME = "methodName";

    /**
     * indicate the parameters in this field in the GET parameters, separated by comas
     */
    const FIELD_PARAMETERS = "parameters";
    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_GET_DESERIALIZER, $this, "getHandlerHook");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_GET_DESERIALIZED_REQUEST_HANDLER, $this, "getHandlerHook");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_GET_EXCEPTION_HANDLER, $this, "getHandlerHook");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_GET_SERIALIZER, $this, "getHandlerHook");
    }

    /**
     * if no content type, then returns this. 
     * @param mixed null at call in gateway.
     * @param String $contentType
     * @return this or null
     */
    public function getHandlerHook($handler, $contentType){
        if(!$contentType){
            return $this;
        }
    }

    /**
     * @see Amfphp_Core_Common_IDeserializer
     */
    public function deserialize($rawPostData, array $getData){
        return $getData;
    }

    /**
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter){
        if(isset ($deserializedRequest[self::FIELD_SERVICE_NAME])){
            $serviceName = $deserializedRequest[self::FIELD_SERVICE_NAME];
        }else{
            throw new Exception(self::FIELD_SERVICE_NAME . " field missing in url's get parameters \n" . print_r($deserializedRequest, true));
        }
        if(isset ($deserializedRequest[self::FIELD_METHOD_NAME])){
            $methodName = $deserializedRequest[self::FIELD_METHOD_NAME];
        }else{
            throw new Exception(self::FIELD_METHOD_NAME . " field missing in url's get parameters \n" . print_r($deserializedRequest, true));
        }
        if(isset ($deserializedRequest[self::FIELD_PARAMETERS])){
            $parameters = explode(",", $deserializedRequest[self::FIELD_PARAMETERS]);
        }else{
            throw new Exception(self::FIELD_PARAMETERS . " field missing in url's get parameters \n" . print_r($deserializedRequest, true));
        }
        return $serviceRouter->executeServiceCall($serviceName, $methodName, $parameters);
        
    }

    /**
     * @see Amfphp_Core_Common_IExceptionHandler
     */
    public function handleException(Exception $exception){
        return str_replace("\n", "<br>", $exception->__toString());
        
    }
    
    /**
     * @see Amfphp_Core_Common_ISerializer
     */
    public function serialize($data){
        return $data;

    }


}
?>
