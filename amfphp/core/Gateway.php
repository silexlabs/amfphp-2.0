<?php

/**
 * where everything comes together in amfphp.
 * The class used for the entry point of a remoting call
 * TODO consider moving everything from context directly to gateway class. A.S.
 *
 * @author Ariel Sommeria-klein
 */
class core_Gateway {

    /**
     * config. 
     * @var <core_Config>
     */
    public $config;

    /**
     * context.
     * @var <core_ServiceCallContext>
     */
    private $context;

    /**
     * hook called when the packet request comes in.
     * @param String $rawData the raw http data
     */
    const HOOK_PACKET_REQUEST_SERIALIZED = "HOOK_PACKET_REQUEST_SERIALIZED";

    /**
     * hook called after the packet request is deserialized
     * @param AMFPacket requestPacket the deserialized packet
     */
    const HOOK_PACKET_REQUEST_DESERIALIZED = "HOOK_PACKET_REQUEST_DESERIALIZED";

    /**
     * hook called when the packet response is ready.
     * @param core_amf_Packet $responsePacket the deserialized packet
     */
    const HOOK_PACKET_RESPONSE_DESERIALIZED = "HOOK_PACKET_RESPONSE_DESERIALIZED";

    /**
     * hook called when the packet response is ready and serialized
     * @param String $rawData the raw http data
     */
    const HOOK_PACKET_RESPONSE_SERIALIZED = "HOOK_PACKET_RESPONSE_SERIALIZED";

    /**
     * hook called when there is an exception
     * @param <Exception> $e the exception object
     * @param <core_amf_Message> $requestMessage the request message that caused the exception
     */
    const HOOK_EXCEPTION_CAUGHT = "HOOK_EXCEPTION_CAUGHT";

    /**
     * hook called for each request header
     * @param core_amf_Header $header the request header
     */
    const HOOK_REQUEST_HEADER = "HOOK_REQUEST_HEADER";


    public function  __construct($rawInputData) {
        $this->context = new core_ServiceCallContext();
        $this->context->rawInputData = $rawInputData;
        $this->config = new core_Config();
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     * 
     * @param core_amf_Message $requestMessage
     * @return core_amf_Message the response Message for the request
     */
    private function handleRequestMessage(core_amf_Message $requestMessage){
        $serviceRouter = new core_common_ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
        $serviceCallParameters = core_common_ServiceCallParameters::createFromAMFMessage($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responsePacket = new core_amf_Packet();
        $responseMessage = new core_amf_Message();
        $responseMessage->data = $ret;
        $responseMessage->targetURI = $requestMessage->responseURI . core_amf_Constants::AMFPHP_CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseURI = "null";
        return $responseMessage;
    }

    /**
     * handles an exception by generating a serialized AMF response with information about the Exception. Tries to use the requestMessage for the response/target uri
     * @param Exception $e
     * @param core_amf_Message $requestMessage the request message that caused it, if it exists
     * @return String
     */
    private function generateResponseForException(Exception $e, core_amf_Message $requestMessage = null){
        $errorPacket = new core_amf_Packet();
        $hookManager = core_HookManager::getInstance();
        $errorResponseMessage = new core_amf_Message();
        if($requestMessage != null && isset ($requestMessage->responseURI)){
            $errorResponseMessage->targetURI = $requestMessage->responseURI . core_amf_Constants::CLIENT_FAILURE_METHOD;
        }else{
            $errorResponseMessage->targetURI = core_amf_Constants::DEFAULT_REQUEST_RESPONSE_URI . core_amf_Constants::CLIENT_FAILURE_METHOD;
        }
        //not specified
        $errorResponseMessage->responseURI = "null";
        $errorResponseMessage->data = $e->__toString();
        array_push($errorPacket->messages, $errorResponseMessage);
        $serializer = new core_amf_Serializer($errorPacket);
        return $serializer->serialize();
        
    }

    
    /**
     * The service method runs the gateway application.  It deserializes the raw data passed into the constructor as an core_amf_Packet, handles the headers,
     * handles the messages as requests to services, and returns the responses from the services
     * It does not however handle output headers, gzip compression, etc. that is the job of the calling script
     *
     * @return <String> the serialized amf packet containg the service responses
     */
    public function service(){
        core_PluginManager::getInstance()->loadPlugins($this->config->pluginsFolder);
        $requestMessage = null;
        $hookManager = core_HookManager::getInstance();
        try{
            if(!$this->context->rawInputData){
                throw new Exception("no raw data passed to gateway");
            }
            //call hook for reading serialized incoming packet
            $hookManager->callHooks(self::HOOK_PACKET_REQUEST_SERIALIZED, array($this->context->rawInputData));

            $deserializer = new core_amf_Deserializer($this->context->rawInputData);
            $requestPacket = $deserializer->deserialize();

            //call hook for reading/modifying request packet
            $fromHooks = $hookManager->callHooks(self::HOOK_PACKET_REQUEST_DESERIALIZED, array($requestPacket));
            $requestPacket = $fromHooks[0];

            $numHeaders = count($requestPacket->headers);
            for($i = 0; $i < $numHeaders; $i++){
                $requestHeader = $requestPacket->headers[$i];
                //handle a header. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
                $hookManager->callHooks(self::HOOK_REQUEST_HEADER, array($requestHeader));
            }

            $numMessages = count($requestPacket->messages);
            $rawOutputData = "";
            $responsePacket = new core_amf_Packet();
            for($i = 0; $i < $numMessages; $i++){
                $requestMessage = $requestPacket->messages[$i];
                $responseMessage = $this->handleRequestMessage($requestMessage);
                array_push($responsePacket->messages, $responseMessage);
            }

            //call hook for reading/modifying response packet
            $fromHooks = $hookManager->callHooks(self::HOOK_PACKET_RESPONSE_DESERIALIZED, array($responsePacket));
            $responsePacket = $fromHooks[0];

            $serializer = new core_amf_Serializer($responsePacket);
            $rawOutputData = $serializer->serialize();

            //call hook for reading serialized response packet
            $hookManager->callHooks(self::HOOK_PACKET_RESPONSE_SERIALIZED, array($rawOutputData));

            return $rawOutputData;

        }catch(Exception $e){
            $serializedErrorMessage = $this->generateResponseForException($e, $requestMessage);

            //call hooks for reading the exception and the request packet that caused it
            $hookManager->callHooks(self::HOOK_EXCEPTION_CAUGHT, array($e, $requestMessage));
            //call hook for reading serialized response packet
            $hookManager->callHooks(self::HOOK_PACKET_RESPONSE_SERIALIZED, array($serializedErrorMessage));
            return $serializedErrorMessage;
        }

    }


}
?>
