<?php
/**
 * interface for deserializers. 
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IDeserializer {
    
    /**
     * the raw data is passed in the constructor. Call this method to execute the deserialization.
     * The return type is mixed for now, as it is unclear what the deserialization of other formats will return. It could be a 
     * generic "Packet" or "packet" type. For now the amf deserializer returns an Amfphp_Core_Amf_Packet
     * @return <mixed> (Amfphp_Core_Amf_Packet)
     */
    public function deserialize();
}
?>
