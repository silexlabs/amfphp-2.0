<?php
class ByteArray
{
	var $data;

	function ByteArray($data)
	{
		$this->data = $data;
	}
}

class RecordSet
{
	var $data;

	function RecordSet($data)
	{
		$this->data = $data;
	}
}


class PageableRecordSet
{
	var $data;
	var $limit;

	function PageableRecordSet($data, $limit = 15)
	{
		$this->data = $data;
		$this->limit = $limit;
	}
}

class AcknowledgeMessage
{
	var $_explicitType = "flex.messaging.messages.AcknowledgeMessage";

	function AcknowledgeMessage($messageId = NULL, $clientId = NULL)
	{
	    $this->messageId = $this->generateRandomId();
	    $this->clientId = $clientId != NULL ? $clientId : $this->generateRandomId();
	    $this->destination = null;
	    $this->body = null;
	    $this->timeToLive = 0;
	    $this->timestamp = (int) (time() . '00');
	    $this->headers = new stdClass();
	    $this->correlationId = $messageId;
	}

	function generateRandomId()
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

class CommandMessage
{
	var $_explicitType = 'flex.messaging.messages.CommandMessage';
}

class RemotingMessage
{
	var $_explicitType = 'flex.messaging.messages.RemotingMessage';
}

class ErrorMessage
{
	var $_explicitType = "flex.messaging.messages.ErrorMessage";
	var $correlationId;
	var $faultCode;
	var $faultDetail;
	var $faultString;
}

?>