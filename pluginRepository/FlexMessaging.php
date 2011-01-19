<?php

/**
 * Support for flex messaging.
 * Flex doesn't use the basic packet system. When using a remote objct, first a CommandMessage is sent, expecting an AcknowledgeMessage in return.
 * Then a RemotingMessage is sent, expecting an AcknowledgeMessage in return.
 * In case of an error, an ErrorMessage is expected
 *
 * @author Ariel Sommeria-Klein
 */
class FlexMessaging{
    const TYPE_FLEX_COMMAND_MESSAGE = 'flex.messaging.messages.CommandMessage';
    const TYPE_FLEX_REMOTING_MESSAGE = 'flex.messaging.messages.RemotingMessage';
    const TYPE_FLEX_ACKNOWLEDGE_MESSAGE = 'flex.messaging.messages.AcknowledgeMessage';
    const TYPE_FLEX_ERROR_MESSAGE = 'flex.messaging.messages.ErrorMessage';
    
    const FIELD_MESSAGE_ID = "messageId";

    /**
     * if this is set, special error handling applies
     * @var Boolean
     */
    private $clientUsesFlexMessaging;
    
    /**
     * the messageId of the last flex message. Used for error generation
     * @var String
     */
    private $lastFlexMessageId;
    public function  __construct() {
        HookManager::getInstance()->addHook(Gateway::HOOK_SPECIAL_REQUEST_HANDLING, array($this, "specialRequestMessageHandler"));
        HookManager::getInstance()->addHook(Gateway::HOOK_EXCEPTION_CAUGHT, array($this, "exceptionCaughtHandler"));
        $this->clientUsesFlexMessaging = false;
    }

    /**
     *
     * @param AMFMessage $requestMessage the request message
     * @param ServiceRouter the service router, if needed
     * @param AMFMessage $responseMessage. null at first call in gateway. If the plugin takes over the handling of the request message,
     * it must set this to a proper AMFMessage
     * @return <array>
     */
    public function specialRequestMessageHandler(AMFMessage $requestMessage, ServiceRouter $serviceRouter, AMFMessage $responseMessage = null){

        //for test purposes
        //throw new AmfphpException(print_r($requestMessage->data[0], true));
        if($responseMessage != null){
            //message has already been handled by another plugin, so don't look any further
            return;
        }

        if($requestMessage->data == null){
            //all flex messages have data
            return;
        }

        $explicitTypeField = AMFConstants::FIELD_EXPLICIT_TYPE;
        $messageIdField = self::FIELD_MESSAGE_ID;

        if(!isset ($requestMessage->data[0]) || !isset ($requestMessage->data[0]->$explicitTypeField)){
        throw new AmfphpException(print_r($requestMessage->data[0], true));
            //and all flex messages have data containing one object with an explicit type
            return;
        }

        
        if($requestMessage->data[0]->$explicitTypeField == self::TYPE_FLEX_COMMAND_MESSAGE){
            $this->clientUsesFlexMessaging = true;
            $command = $requestMessage->data[0];
            //command message. An empty AcknowledgeMessage is expected.
            $acknowledge = new AcknowledgeMessage($command->$messageIdField);
            $responseMessage = new AMFMessage($requestMessage->responseURI . AMFConstants::AMFPHP_CLIENT_SUCCESS_METHOD, null, $acknowledge);

        }

        
        if($requestMessage->data[0]->$explicitTypeField == self::TYPE_FLEX_REMOTING_MESSAGE){
            $this->clientUsesFlexMessaging = true;
            $remoting = $requestMessage->data[0];
            //remoting message. An AcknowledgeMessage with the result of the service call is expected.
            $serviceCallResult = $serviceRouter->executeServiceCall($remoting->source, $remoting->operation, $remoting->body);
            $acknowledge = new AcknowledgeMessage($remoting->$messageIdField);
            $acknowledge->body = $serviceCallResult;
            $responseMessage = new AMFMessage($requestMessage->responseURI . AMFConstants::AMFPHP_CLIENT_SUCCESS_METHOD, null, $acknowledge);

        }
        if($responseMessage != null){
            return array($requestMessage, $serviceRouter, $responseMessage);
        }

    }

    /**
     * flex expects error messages formatted in a special way, using the ErrorMessage object.
     * 
     * @param Exception $e
     * @param AMFMessage $requestMessage
     * @param AMFMessage $responseMessage
     */
    public function exceptionCaughtHandler(Exception $e, AMFMessage $requestMessage = null, AMFMessage $responseMessage = null){
        if(!$this->clientUsesFlexMessaging){
            return;
        }
        
        $error = new ErrorMessage($this->lastFlexMessageId);
        $error->faultCode = $e->getCode();
        $error->faultString = $e->getMessage();
        $error->faultDetail = $e->getTraceAsString();
        $responseMessage = new AMFMessage($requestMessage->responseURI . AMFConstants::CLIENT_FAILURE_METHOD, null, $error);
        return array($e, $requestMessage, $responseMessage);
    }
}

class ErrorMessage
{
	public $_explicitType;
	public $correlationId;
	public $faultCode;
	public $faultDetail;
	public $faultString;

        public function  __construct($correlationId) {
            $explicitTypeField = AMFConstants::FIELD_EXPLICIT_TYPE;
            $this->$explicitTypeField = FlexMessaging::TYPE_FLEX_ERROR_MESSAGE;
	    $this->correlationId = $correlationId;
        }
}

class AcknowledgeMessage
{
	public $_explicitType;
	public $correlationId;
        public $messageId;
        public $clientId;
        public $destination;
        public $body;
        public $timeToLive;
        public $timeStamp;
        public $headers;

	public function  __construct($correlationId)
	{
            $explicitTypeField = AMFConstants::FIELD_EXPLICIT_TYPE;
            $this->$explicitTypeField = FlexMessaging::TYPE_FLEX_ACKNOWLEDGE_MESSAGE;
	    $this->correlationId = $correlationId;
	    $this->messageId = $this->generateRandomId();
	    $this->clientId = $this->generateRandomId();
	    $this->destination = null;
	    $this->body = null;
	    $this->timeToLive = 0;
	    $this->timestamp = (int) (time() . '00');
	    $this->headers = new stdClass();
	}

	public function generateRandomId()
	{
	   // version 4 UUID
	   return sprintf(
	       '%08X-%04X-%04X-%02X%02X-%012X',
	       mt_rand(),
	       mt_rand(0, 65535),
	       bindec(substr_replace(
	           sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)
	       ),
	       bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
	       mt_rand(0, 255),
	       mt_rand()
	   );
	}
}

?>
