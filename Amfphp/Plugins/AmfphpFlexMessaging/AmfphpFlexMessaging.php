<?php
require_once dirname(__FILE__) . "/AcknowledgeMessage.php";
require_once dirname(__FILE__) . "/ErrorMessage.php";
/**
 * Support for flex messaging.
 * Flex doesn't use the basic packet system. When using a remote objct, first a CommandMessage is sent, expecting an AcknowledgeMessage in return.
 * Then a RemotingMessage is sent, expecting an AcknowledgeMessage in return.
 * In case of an error, an ErrorMessage is expected
 *
 * @author Ariel Sommeria-Klein
 */
class AmfphpFlexMessaging{
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
    public function  __construct(array $config = null) {
        Amfphp_Core_HookManager::getInstance()->addHook(Amfphp_Core_Gateway::HOOK_SPECIAL_REQUEST_HANDLING, array($this, "specialRequestMessageHandler"));
        Amfphp_Core_HookManager::getInstance()->addHook(Amfphp_Core_Gateway::HOOK_EXCEPTION_CAUGHT, array($this, "exceptionCaughtHandler"));
        $this->clientUsesFlexMessaging = false;
    }

    /**
     *
     * @param Amfphp_Core_Amf_Message $requestMessage the request message
     * @param Amfphp_Core_Common_ServiceRouter the service router, if needed
     * @param Amfphp_Core_Amf_Message $responseMessage. null at first call in gateway. If the plugin takes over the handling of the request message,
     * it must set this to a proper Amfphp_Core_Amf_Message
     * @return <array>
     */
    public function specialRequestMessageHandler(Amfphp_Core_Amf_Message $requestMessage, Amfphp_Core_Common_ServiceRouter $serviceRouter, Amfphp_Core_Amf_Message $responseMessage = null){

        //for test purposes
        //throw new Amfphp_Core_Exception(print_r($requestMessage->data[0], true));
        if($responseMessage != null){
            //message has already been handled by another plugin, so don't look any further
            return;
        }

        if($requestMessage->data == null){
            //all flex messages have data
            return;
        }

        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $messageIdField = self::FIELD_MESSAGE_ID;

        if(!isset ($requestMessage->data[0]) || !isset ($requestMessage->data[0]->$explicitTypeField)){
            //and all flex messages have data containing one object with an explicit type
            return;
        }

        
        if($requestMessage->data[0]->$explicitTypeField == self::TYPE_FLEX_COMMAND_MESSAGE){
            $this->clientUsesFlexMessaging = true;
            $command = $requestMessage->data[0];
            //command message. An empty AcknowledgeMessage is expected.
            $acknowledge = new AmfphpFlexMessaging_AcknowledgeMessage($command->$messageIdField);
            $responseMessage = new Amfphp_Core_Amf_Message($requestMessage->responseURI . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD, null, $acknowledge);

        }

        
        if($requestMessage->data[0]->$explicitTypeField == self::TYPE_FLEX_REMOTING_MESSAGE){
            $this->clientUsesFlexMessaging = true;
            $remoting = $requestMessage->data[0];
            //remoting message. An AcknowledgeMessage with the result of the service call is expected.
            $serviceCallResult = $serviceRouter->executeServiceCall($remoting->source, $remoting->operation, $remoting->body);
            $acknowledge = new AmfphpFlexMessaging_AcknowledgeMessage($remoting->$messageIdField);
            $acknowledge->body = $serviceCallResult;
            $responseMessage = new Amfphp_Core_Amf_Message($requestMessage->responseURI . Amfphp_Core_Amf_Constants::CLIENT_SUCCESS_METHOD, null, $acknowledge);

        }
        if($responseMessage != null){
            return array($requestMessage, $serviceRouter, $responseMessage);
        }

    }

    /**
     * flex expects error messages formatted in a special way, using the ErrorMessage object.
     * 
     * @param Exception $e
     * @param Amfphp_Core_Amf_Message $requestMessage
     * @param Amfphp_Core_Amf_Message $responseMessage
     */
    public function exceptionCaughtHandler(Exception $e, Amfphp_Core_Amf_Message $requestMessage = null, Amfphp_Core_Amf_Message $responseMessage = null){
        if(!$this->clientUsesFlexMessaging){
            return;
        }
        
        $error = new AmfphpFlexMessaging_ErrorMessage($this->lastFlexMessageId);
        $error->faultCode = $e->getCode();
        $error->faultString = $e->getMessage();
        $error->faultDetail = $e->getTraceAsString();
        $responseMessage = new Amfphp_Core_Amf_Message($requestMessage->responseURI . Amfphp_Core_Amf_Constants::CLIENT_FAILURE_METHOD, null, $error);
        return array($e, $requestMessage, $responseMessage);
    }
}

?>
