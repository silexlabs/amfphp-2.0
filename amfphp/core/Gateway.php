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
     * @param <type> $requestBody
     * @return AMFBody the response body for the request
     */
    private function handleRequest($requestBody){
        $serviceRouter = new ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
        $ret = $serviceRouter->executeServiceCall($requestBody->serviceName, $requestBody->functionName, $requestBody->data);
        $responseMessage = new AMFMessage();
        $responseBody = new AMFBody();
        $responseBody->data = $ret;
        $responseBody->responseURI = $requestBody->responseURI . "/onResult";
        //not specified
        $responseBody->targetURI = "null";
        return $responseBody;
    }

    /**
     * handles an exception by generating a serialized AMF response with information about the Exception. Tries to use the requestBody for the response/target uri
     * @param Exception $e
     * @param AMFBody $requestBody
     * @return <String>
     */
    private function generateResponseForException(Exception $e, AMFBody $requestBody = null){
        $errorMessage = new AMFMessage();
        $errorResponseBody = new AMFBody();
        if($requestBody != null && isset ($requestBody->responseURI)){
            $errorResponseBody->responseURI = $requestBody->responseURI . "/onStatus";
        }else{
            $errorResponseBody->targetURI = "/1/onStatus";
        }
        //not specified
        $errorResponseBody->targetURI = "null";
        $errorResponseBody->data = $e->__toString();
        $errorMessage->addBody($errorResponseBody);
        $serializer = new AMFSerializer($errorMessage);
        return $serializer->serialize();
        
    }
    
    /**
     * The service method runs the gateway application.  It turns the gateway 'on'.  You
     * have to call the service method as the last line of the gateway script after all of the
     * gateway configuration properties have been set.
     *
     * @return <String>
     */
    public function service(){
        $requestBody = null;
        try{
            $deserializer = new DummyDeserializer($this->context->rawInputData);
            $requestMessage = $deserializer->deserialize();
            //TODO handle headers
            $numBodies = $requestMessage->numBodies();
            $rawOutputData = "";
            $responseMessage = new AMFMessage();
            for($i = 0; $i < $numBodies; $i++){
                $requestBody = $requestMessage->getBodyAt();
                $responseBody = $this->handleRequest($requestBody);
                $responseMessage->addBody($responseBody);
            }
            $serializer = new AMFSerializer($responseMessage);
            $rawOutputData = $serializer->serialize();
            return $rawOutputData;

        }catch(Exception $e){
            return $this->generateResponseForException($e, $requestBody);
        }

    }

}
?>
