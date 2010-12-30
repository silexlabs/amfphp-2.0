<?php
/**
 * a dummy serializer, while waiting for the real thing...
 *
 * @author Ariel Sommeria-klein
 */
class DummyDeserializer implements IDeserializer{
    //put your code here

    public function __construct($raw)
    {
    }

    /**
     * the main function of the deserializer
     * @return AMFMessage
     */
    public function deserialize(){

        $body = new AMFBody();
        $body->targetURI = "MirrorService/mirrorFunction";
        $body->serviceName = "MirrorService";
        $body->functionName = "returnOneParam";
        $body->responseURI = "/1";
        $body->data = array("testString");
        $message = new AMFMessage();
        $message->amfVersion = 0;
        $message->addBody($body);
        return $message;
        
    }

}
?>
