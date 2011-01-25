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
     * config. 
     * @var Amfphp_Core_Config
     */
    public $config;

    /**
     * context.
     * @var Amfphp_Core_ServiceCallContext
     */
    private $context;

    /**
     * hook called when the packet request comes in.
     * @param String $rawData the raw http data
     */
    const HOOK_REQUEST_SERIALIZED = "HOOK_REQUEST_SERIALIZED";

    /**
     * hook called after the packet request is deserialized
     * @param Amfphp_Core_Amf_Packet requestPacket the deserialized packet
     */
    const HOOK_REQUEST_DESERIALIZED = "HOOK_REQUEST_DESERIALIZED";

    /**
     * hook called when the packet response is ready.
     * @param Amfphp_Core_Amf_Packet $responsePacket the deserialized packet
     */
    const HOOK_RESPONSE_DESERIALIZED = "HOOK_RESPONSE_DESERIALIZED";

    /**
     * hook called when the packet response is ready and serialized
     * @param String $rawData the raw http data
     */
    const HOOK_RESPONSE_SERIALIZED = "HOOK_RESPONSE_SERIALIZED";

    /**
     * hook called when there is an exception
     * @param Exception $e the exception object
     * @param Amfphp_Core_Amf_Message $requestMessage the request message that caused the exception
     * @param Amfphp_Core_Amf_Message $responseMessage. null at first call in gateway. If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Amf_Message
     */
    const HOOK_EXCEPTION_CAUGHT = "HOOK_EXCEPTION_CAUGHT";

    /**
     * hook called for each request header
     * @param Amfphp_Core_Amf_Header $header the request header
     */
    const HOOK_REQUEST_HEADER = "HOOK_REQUEST_HEADER";

    /**
     * hook called to give plugins a chance to handle the request message instead of passing it to the service router
     * @param Amfphp_Core_Amf_Message $requestMessage the request message
     * @param ServiceRouter the service router, if needed
     * @param Amfphp_Core_Amf_Message $responseMessage. null at first call in gateway. If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Amf_Message
     * to indicate
     */
    const HOOK_SPECIAL_REQUEST_HANDLING = "HOOK_SPECIAL_REQUEST_HANDLING";



    public function  __construct($rawInputData) {
        $this->context = new Amfphp_Core_ServiceCallContext();
        $this->context->rawInputData = $rawInputData;
        $this->config = new Amfphp_Core_Config();
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     * 
     * @param Amfphp_Core_Amf_Message $requestMessage
     * @return Amfphp_Core_Amf_Message the response Message for the request
     */
    private function handleRequestMessage(Amfphp_Core_Amf_Message $requestMessage){
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $serviceRouter = new Amfphp_Core_Common_ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
        $ret = $hookManager->callHooks(self::HOOK_SPECIAL_REQUEST_HANDLING, array($requestMessage, $serviceRouter, null));
        if($ret && ($ret[2] != null)){
            return $ret[2];
        }
        
        //plugins didn't do any special handling. Assumes this is a simple Amfphp_Core_Amf_ RPC call
        $serviceCallParameters = Amfphp_Core_Common_ServiceCallParameters::createFromAmfphp_Core_Amf_Message($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responseMessage = new Amfphp_Core_Amf_Message();
        $responseMessage->data = $ret;
        $responseMessage->targetURI = $requestMessage->responseURI . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseURI = "null";
        return $responseMessage;
    }

    /**
     * handles an exception by generating a serialized Amf response with information about the Exception. Tries to use the requestMessage for the response/target uri
     * @param Exception $e
     * @param Amfphp_Core_Amf_Message $requestMessage the request message that caused it, if it exists
     * @return String the serialized error message
     */
    private function generateResponseForException(Exception $e, Amfphp_Core_Amf_Message $requestMessage = null){
        $errorPacket = new Amfphp_Core_Amf_Packet();
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $errorResponseMessage = null;
        $ret = $hookManager->callHooks(self::HOOK_EXCEPTION_CAUGHT, array($e, $requestMessage, null));
        if($ret && ($ret[2] != null)){
            $errorResponseMessage = $ret[2];
        }else{
            //no special handling by plugins. generate a basic error Amfphp_Core_Amf_Message
            $errorResponseMessage = new Amfphp_Core_Amf_Message();
            if($requestMessage != null && isset ($requestMessage->responseURI)){
                $errorResponseMessage->targetURI = $requestMessage->responseURI . Amfphp_Core_Amf_Constants::CLIENT_FAILURE_METHOD;
            }else{
                $errorResponseMessage->targetURI = Amfphp_Core_Amf_Constants::DEFAULT_REQUEST_RESPONSE_URI . Amfphp_Core_Amf_Constants::CLIENT_FAILURE_METHOD;
            }
            //not specified
            $errorResponseMessage->responseURI = "null";
            $errorResponseMessage->data = new stdClass();
            $errorResponseMessage->data->faultCode = $e->getCode();
            $errorResponseMessage->data->faultString = $e->getMessage();
            $errorResponseMessage->data->faultDetail = $e->getTraceAsString();
        }

        array_push($errorPacket->messages, $errorResponseMessage);
        $serializer = new Amfphp_Core_Amf_Serializer($errorPacket);
        return $serializer->serialize();
        
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
        $requestMessage = null;
        try{
            Amfphp_Core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolder, $this->config->pluginsConfig, $this->config->disabledPlugins);
            if(!$this->context->rawInputData){
                throw new Amfphp_Core_Exception("no raw data passed to gateway");
            }
            //call hook for reading serialized incoming packet
            $hookManager->callHooks(self::HOOK_REQUEST_SERIALIZED, array($this->context->rawInputData));

            $deserializer = new Amfphp_Core_Amf_Deserializer($this->context->rawInputData);
            $requestPacket = $deserializer->deserialize();

            //call hook for reading/modifying request packet
            $fromHooks = $hookManager->callHooks(self::HOOK_REQUEST_DESERIALIZED, array($requestPacket));
            $requestPacket = $fromHooks[0];

            $numHeaders = count($requestPacket->headers);
            for($i = 0; $i < $numHeaders; $i++){
                $requestHeader = $requestPacket->headers[$i];
                //handle a header. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
                $hookManager->callHooks(self::HOOK_REQUEST_HEADER, array($requestHeader));
            }

            $numMessages = count($requestPacket->messages);
            $rawOutputData = "";
            $responsePacket = new Amfphp_Core_Amf_Packet();
            for($i = 0; $i < $numMessages; $i++){
                $requestMessage = $requestPacket->messages[$i];
                $responseMessage = $this->handleRequestMessage($requestMessage);
                array_push($responsePacket->messages, $responseMessage);
            }

            //call hook for reading/modifying response packet
            $fromHooks = $hookManager->callHooks(self::HOOK_RESPONSE_DESERIALIZED, array($responsePacket));
            $responsePacket = $fromHooks[0];

            $serializer = new Amfphp_Core_Amf_Serializer($responsePacket);
            $rawOutputData = $serializer->serialize();


        }catch(Exception $e){
            $rawOutputData = $this->generateResponseForException($e, $requestMessage);
        }
        //call hook for reading serialized response packet
        $hookManager->callHooks(self::HOOK_RESPONSE_SERIALIZED, array($rawOutputData));

        return $rawOutputData;

    }


}
?>
