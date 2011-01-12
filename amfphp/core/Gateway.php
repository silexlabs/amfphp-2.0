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



    public function  __construct($rawInputData) {
        $this->context = new ServiceCallContext();
        $this->context->rawInputData = $rawInputData;
        $this->config = new AmfphpConfig();
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     * 
     * @param <type> $requestMessage
     * @return AMFMessage the response Message for the request
     */
    private function handleRequest(AMFMessage $requestMessage){
        $serviceRouter = new ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
        $serviceCallParameters = ServiceCallParameters::createFromAMFMessage($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responsePacket = new AMFPacket();
        $responseMessage = new AMFMessage();
        $responseMessage->data = $ret;
        $responseMessage->targetURI = $requestMessage->responseURI . "/onResult";
        //not specified
        $responseMessage->responseURI = "null";
        return $responseMessage;
    }

    /**
     * handles an exception by generating a serialized AMF response with information about the Exception. Tries to use the requestMessage for the response/target uri
     * @param Exception $e
     * @param AMFMessage $requestMessage
     * @return <String>
     */
    private function generateResponseForException(Exception $e, AMFMessage $requestMessage = null){
        $errorPacket = new AMFPacket();
        $errorResponseMessage = new AMFMessage();
        if($requestMessage != null && isset ($requestMessage->responseURI)){
            $errorResponseMessage->targetURI = $requestMessage->responseURI . "/onStatus";
        }else{
            $errorResponseMessage->targetURI = "/1/onStatus";
        }
        //not specified
        $errorResponseMessage->responseURI = "null";
        $errorResponseMessage->data = $e->__toString();
        $errorPacket->addMessage($errorResponseMessage);
        $serializer = new AMFSerializer($errorPacket);
        return $serializer->serialize();
        
    }
    
    /**
     * The service method runs the gateway application.  It deserializes the raw data passed into the constructor as an AMFPacket, handles the headers,
     * handles the messages as requests to services, and returns the responses from the services
     * It does not however handle output headers, gzip compression, etc. that is the job of the calling script
     * @TODO handle headers. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
     *
     * @return <String> the serialized amf packet containg the service responses
     */
    public function service(){
        $requestMessage = null;
        try{
            $deserializer = new AMFDeserializer($this->context->rawInputData);
            $requestPacket = $deserializer->deserialize();
            $numMessages = $requestPacket->numMessages();
            $rawOutputData = "";
            $responsePacket = new AMFPacket();
            for($i = 0; $i < $numMessages; $i++){
                $requestMessage = $requestPacket->getMessageAt();
                $responseMessage = $this->handleRequest($requestMessage);
                $responsePacket->addMessage($responseMessage);
            }
            $serializer = new AMFSerializer($responsePacket);
            $rawOutputData = $serializer->serialize();
            return $rawOutputData;

        }catch(Exception $e){
            return $this->generateResponseForException($e, $requestMessage);
        }

    }


}
?>
