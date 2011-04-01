<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Core_Amf
 */

/**
 * This is the default handler for the gateway. It's job is to handle everything that is specific to Amf for the gateway.
 * @package Amfphp_Core_Amf
 * @author Ariel Sommeria-Klein
 */
class Amfphp_Core_Amf_Handler implements Amfphp_Core_Common_IDeserializer, Amfphp_Core_Common_IDeserializedRequestHandler, Amfphp_Core_Common_IExceptionHandler, Amfphp_Core_Common_ISerializer{


    /**
     * filter called for each amf request header, to give a plugin the chance to handle it.
     * Unless a plugin handles them, amf headers are ignored
     * Headers embedded in the serialized requests are regarded to be a Amf specific, so they get their filter in Amf Handler
     * @param Object $handler. null at call. Return if the plugin can handle
     * @param Amfphp_Core_Amf_Header $header the request header
     * @todo consider an interface for $handler. Maybe overkill here
     */
    const FILTER_AMF_REQUEST_HEADER_HANDLER = "FILTER_AMF_REQUEST_HEADER_HANDLER";

    /**
     * filter called for each amf request message, to give a plugin the chance to handle it.
     * This is for the Flex Messaging plugin to be able to intercept the message and say it wants to handle it
     * @param Object $handler. null at call. Return if the plugin can handle
     * @param Amfphp_Core_Amf_Message $requestMessage the request message
     * @todo consider an interface for $handler. Maybe overkill here
     */
    const FILTER_AMF_REQUEST_MESSAGE_HANDLER = "FILTER_AMF_REQUEST_MESSAGE_HANDLER";

    /**
     * filter called for exception handling an Amf packet/message, to give a plugin the chance to handle it.
     * This is for the Flex Messaging plugin to be able to intercept the exception and say it wants to handle it
     * @param Object $handler. null at call. Return if the plugin can handle
     * @todo consider an interface for $handler. Maybe overkill here
     */
    const FILTER_AMF_EXCEPTION_HANDLER = "FILTER_AMF_EXCEPTION_HANDLER";

    /**
     * Amf specifies that an error message must be aimed at an end point. This stores the last message's response Uri to be able to give this end point
     * in case of an exception during the handling of the message. The default is "/1", because a response Uri is not always available
     * @var String
     */
    private $lastRequestMessageResponseUri;

	private $objectEncoding = Amfphp_Core_Amf_Constants::AMF0_ENCODING;

    public function  __construct() {
        $this->lastRequestMessageResponseUri = "/1";
    }
    
    /**
     * @see Amfphp_Core_Common_IDeserializer
     */
    public function deserialize(array $getData, array $postData, $rawPostData){
        $deserializer = new Amfphp_Core_Amf_Deserializer($rawPostData);
        $requestPacket = $deserializer->deserialize();

	    $this->objectEncoding = $requestPacket->amfVersion;

	    return $requestPacket;
    }

    /**
     * creates a ServiceCallParamaeters object from an Amfphp_Core_Amf_Message
     * supported separators in the targetUri are "/" and "."
     * @param Amfphp_Core_Amf_Message $Amfphp_Core_Amf_Message
     * @return Amfphp_Core_Common_ServiceCallParameters
     */
    private function getServiceCallParameters(Amfphp_Core_Amf_Message $Amfphp_Core_Amf_Message){
        $targetUri = str_replace(".", "/", $Amfphp_Core_Amf_Message->targetUri);
        $split = explode("/", $targetUri);
        $ret = new Amfphp_Core_Common_ServiceCallParameters();
        $ret->methodName = array_pop($split);
        $ret->serviceName = join($split, "/");
        $ret->methodParameters = $Amfphp_Core_Amf_Message->data;
        return $ret;
    }

    /**
     * process a request and generate a response.
     * throws an Exception if anything fails, so caller must encapsulate in try/catch
     *
     * @param Amfphp_Core_Amf_Message $requestMessage
     * @return Amfphp_Core_Amf_Message the response Message for the request
     */
    private function handleRequestMessage(Amfphp_Core_Amf_Message $requestMessage, Amfphp_Core_Common_ServiceRouter $serviceRouter){
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $fromFilters = $filterManager->callFilters(self::FILTER_AMF_REQUEST_MESSAGE_HANDLER, null, $requestMessage);
        if($fromFilters){
            $handler = $fromFilters;
            return $handler->handleRequestMessage($requestMessage, $serviceRouter);
        }
        
        //plugins didn't do any special handling. Assumes this is a simple Amfphp_Core_Amf_ RPC call
        $serviceCallParameters = $this->getServiceCallParameters($requestMessage);
        $ret = $serviceRouter->executeServiceCall($serviceCallParameters->serviceName, $serviceCallParameters->methodName, $serviceCallParameters->methodParameters);
        $responseMessage = new Amfphp_Core_Amf_Message();
        $responseMessage->data = $ret;
        $responseMessage->targetUri = $requestMessage->responseUri . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD;
        //not specified
        $responseMessage->responseUri = "null";
        return $responseMessage;
    }


    /**
     * @see Amfphp_Core_Common_IDeserializedRequestHandler
     */
    public function handleDeserializedRequest($deserializedRequest, Amfphp_Core_Common_ServiceRouter $serviceRouter){
        $numHeaders = count($deserializedRequest->headers);
        for($i = 0; $i < $numHeaders; $i++){
            $requestHeader = $deserializedRequest->headers[$i];
            //handle a header. This is a job for plugins, unless comes a header that is so fundamental that it needs to be handled by the core
            $fromFilters = Amfphp_Core_FilterManager::getInstance()->callFilters(self::FILTER_AMF_REQUEST_HEADER_HANDLER, null, $requestHeader);
            if($fromFilters){
                $handler = $fromFilters;
                $handler->handleRequestHeader($requestHeader);
            }
        }

        $numMessages = count($deserializedRequest->messages);
        $rawOutputData = "";
        $responsePacket = new Amfphp_Core_Amf_Packet();
        $responsePacket->amfVersion = $deserializedRequest->amfVersion;
        for($i = 0; $i < $numMessages; $i++){
            $requestMessage = $deserializedRequest->messages[$i];
            $this->lastRequestMessageResponseUri = $requestMessage->responseUri;
            $responseMessage = $this->handleRequestMessage($requestMessage, $serviceRouter);
            $responsePacket->messages[] = $responseMessage;
        }
        return $responsePacket;

    }

    /**
     * @see Amfphp_Core_Common_IExceptionHandler
     */
    public function handleException(Exception $exception){
        $errorPacket = new Amfphp_Core_Amf_Packet();
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $fromFilters = $filterManager->callFilters(self::FILTER_AMF_EXCEPTION_HANDLER, null);
        if($fromFilters){
            $handler = $fromFilters;
            return $handler->generateErrorResponse($exception);
        }

        //no special handling by plugins. generate a simple error response with information about the exception
        $errorResponseMessage = null;
        $errorResponseMessage = new Amfphp_Core_Amf_Message();
        $errorResponseMessage->targetUri = $this->lastRequestMessageResponseUri . Amfphp_Core_Amf_Constants::CLIENT_FAILURE_METHOD;
        //not specified
        $errorResponseMessage->responseUri = "null";
        $errorResponseMessage->data = new stdClass();
        $errorResponseMessage->data->faultCode = $exception->getCode();
        $errorResponseMessage->data->faultString = $exception->getMessage();
        $errorResponseMessage->data->faultDetail = $exception->getTraceAsString();

        $errorPacket->messages[] = $errorResponseMessage;
        return $errorPacket;
        
    }

    /**
     * @see Amfphp_Core_Common_ISerializer
     */
    public function serialize($data){
	    $data->amfVersion = $this->objectEncoding;

        $serializer = new Amfphp_Core_Amf_Serializer($data);
        return $serializer->serialize();

    }

}
?>
