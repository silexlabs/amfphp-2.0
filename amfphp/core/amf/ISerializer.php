<?php
/**
 * interface for deserializers. 
 * @author Ariel Sommeria-klein
 */
interface IDeserializer {
    
    /**
     * the raw data is passed in the constructor. Call this method to execute the deserialization.
     * The return type is mixed for now, as it is unclear what the deserialization of other formats will return. It could be a 
     * generic "message" or "packet" type. For now the amf deserializer returns an AMFMessage
     * @return <mixed> (AMFMessage)
     */
    public function deserialize();
}
?>
