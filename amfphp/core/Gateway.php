<?php

/**
 * where everything comes together in amfphp.
 * The class used for the entry point of a remoting call
 * TODO consider moving everything from context directly to gateway class. A.S.
 *
 * @author Ariel Sommeria-klein
 */
class Gateway {

    /**
     * config. 
     * @var <AmfphpConfig>
     */
    public $config;

    /**
     * context.
     * @var <ServiceCallContext>
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
     * @param AMFPacket $responsePacket the deserialized packet
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
     * @param <AMFMessage> $requestMessage the request message that caused the exception
     */
    const HOOK_EXCEPTION_CAUGHT = "HOOK_EXCEPTION_CAUGHT";

    /**
     * hook called for each request header
     * @param AMFHeader $header the request header
     */
    const HOOK_REQUEST_HEADER = "HOOK_REQUEST_HEADER";


    public function  __construct($rawInputData) {
        $this->context = new ServiceCallContext();
        $this->context->rawInputData = $rawInputData;
        $this->config = new AmfphpConfig();
        PluginManager::getInstance()->loadPlugins(AMFPHP_ROOTPATH . "/plugins/");
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     * 
     * @param AMFMessage $requestMessage
     * @return AMFMessage the response Message for the request
     */
    private function handleRequestMessage(AMFMessage $requestMessage){
        $serviceRouter = new ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
        $serviceCallParameters = ServiceCallParameters::createFromAMFMessage($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responsePacket = new AMFPacket();
        $responseMessage = new AMFMessage();
        $responseMessage->data = $ret;
        $responseMessage->targetURI = $requestMessage->responseURI . AMFConstants::AMFPHP_CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseURI = "null";
        return $responseMessage;
    }

    /**
     * handles an exception by generating a serialized AMF response with information about the Exception. Tries to use the requestMessage for the response/target uri
     * @param Exception $e
     * @param AMFMessage $requestMessage the request message that caused it, if it exists
     * @return String
     */
    private function generateResponseForException(Exception $e, AMFMessage $requestMessage = null){
        $errorPacket = new AMFPacket();
        $hookManager = HookManager::getInstance();
        $errorResponseMessage = new AMFMessage();
        if($requestMessage != null && isset ($requestMessage->responseURI)){
            $errorResponseMessage->targetURI = $requestMessage->responseURI . AMFConstants::CLIENT_FAILURE_METHOD;
        }else{
            $errorResponseMessage->targetURI = AMFConstants::DEFAULT_REQUEST_RESPONSE_URI . AMFConstants::CLIENT_FAILURE_METHOD;
        }
        //not specified
        $errorResponseMessage->responseURI = "null";
        $errorResponseMessage->data = $e->__toString();
        array_push($errorPacket->messages, $errorResponseMessage);
        $serializer = new AMFSerializer($errorPacket);
        return $serializer->serialize();
        
    }

    
    /**
     * The service method runs the gateway application.  It deserializes the raw data passed into the constructor as an AMFPacket, handles the headers,
     * handles the messages as requests to services, and returns the responses from the services
     * It does not however handle output headers, gzip compression, etc. that is the job of the calling script
     *
     * @return <String> the serialized amf packet containg the service responses
     */
    public function service(){
        $requestMessage = null;
        $hookManager = HookManager::getInstance();
        try{
            if(!$this->context->rawInputData){
                throw new Exception("no raw data passed to gateway");
            }
            //call hook for reading serialized incoming packet
            $hookManager->callHooks(self::HOOK_PACKET_REQUEST_SERIALIZED, array($this->context->rawInputData));

            $deserializer = new AMFDeserializer($this->context->rawInputData);
            $requestPacket = $deserializer->deserialize();

            //call hook for reading/modifying request packet
            $fromHooks = $hookManager->callHooks(self::HOOK_PACKET_REQUEST_DESERIALIZED, array($requestPacket));
            $requestPacket = $fromHooks[0];

            $numHeaders = count($requestPacket->headers);
            for($i = 0; $i < $numHeaders; $i++){
                $requestHeader = $requestPacket->headers[$i];
                //handle a header. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
                $hookManager->callHooks(self::HOOK_REQUEST_HEADER, $requestHeader);
            }

            $numMessages = count($requestPacket->messages);
            $rawOutputData = "";
            $responsePacket = new AMFPacket();
            for($i = 0; $i < $numMessages; $i++){
                $requestMessage = $requestPacket->messages[$i];
                $responseMessage = $this->handleRequestMessage($requestMessage);
                array_push($responsePacket->messages, $responseMessage);
            }

            //call hook for reading/modifying response packet
            $fromHooks = $hookManager->callHooks(self::HOOK_PACKET_RESPONSE_DESERIALIZED, array($responsePacket));
            $responsePacket = $fromHooks[0];

            $serializer = new AMFSerializer($responsePacket);
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
