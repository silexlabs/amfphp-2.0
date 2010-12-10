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
     * The service method runs the gateway application.  It turns the gateway 'on'.  You
     * have to call the service method as the last line of the gateway script after all of the
     * gateway configuration properties have been set.
     *
     * @return <String>
     */
    public function service(){
        $rawOutputData = null;
        try{
            $deserializer = new DummyDeserializer($this->context->rawInputData);
            $requestMessage = $deserializer->deserialize();
            $serviceRouter = new ServiceRouter($this->config->serviceFolderPaths, $this->config->serviceNames2ClassFindInfo);
            //TODO handle headers
            //TODO handle multiple bodies.  A.S.
            $requestBody = $requestMessage->getBodyAt();
            $ret = $serviceRouter->executeServiceCall($requestBody->serviceName, $requestBody->functionName, $requestBody->data);

            //TODO create a new class to encapsulate response generation
            //call something like:
            //$r = new ResponseHandler($requestMessage, $ret); $r->getResponse();
            $responseMessage = new AMFMessage();
            $responseBody = new AMFBody();
            $responseBody->data = $ret;
            $responseBody->responseURI = $requestBody->responseURI . "/onResult";
            //not specified
            $responseBody->targetURI = "";
            $responseMessage->addBody($responseBody);
            $serializer = new AMFSerializer($responseMessage);
            $rawOutputData = $serializer->serialize();

        }catch(Exception $exception){
            $exceptionHandler = new AMFExceptionHandler();
            $errorMessage = $exceptionHandler->handle($exception);
            $serializer = new AMFSerializer($errorMessage);
            return $exception->__toString();
            $rawOutputData = $exception;
            $rawOutputData = $serializer->serialize();
        }
        return $rawOutputData;

    }

}
?>
