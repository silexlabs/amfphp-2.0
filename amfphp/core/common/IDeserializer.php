<?php
/**
 * interface for deserializers. 
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IDeserializer {
    
    /**
     * deserialize the data. 
     * @param String $rawData 
     * @return mixed the deserialized data. For example an Amf packet.
     */
    public function deserialize($rawData);
}
?>
