<?php
/**
 * Used to generate a Flex Error message.
 * part of the AmfphpFlexMessaging plugin
 *
 * @author Ariel Sommeria-Klein
 */



class AmfphpFlexMessaging_ErrorMessage
{
	public $_explicitType;
	public $correlationId;
	public $faultCode;
	public $faultDetail;
	public $faultString;

        public function  __construct($correlationId) {
            $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
            $this->$explicitTypeField = AmfphpFlexMessaging::TYPE_FLEX_ERROR_MESSAGE;
	    $this->correlationId = $correlationId;
        }
}
?>
