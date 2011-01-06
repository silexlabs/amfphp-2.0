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
        $nestedAssoc = unserialize(file_get_contents("nestedAssocArray.txt"));

        $body = new AMFBody("MirrorService/returnOneParam", "/1", array($nestedAssoc));
        $message = new AMFMessage();
        $message->amfVersion = 3;
        $message->addBody($body);
     
        return $message;
        
    }

}
?>
