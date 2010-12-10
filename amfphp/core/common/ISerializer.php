<?php
/**
 * interface for serializers. 
 * @author Ariel Sommeria-klein
 */
interface ISerializer {
    
    /**
     * the message and any meta data (such as using amf0 or amf3 for example) are passed in the constructor.
     * Calling this executes the serialization. The return type is noted as a String, but is a binary stream. echo it to the output buffer
     * @return <String>
     */
    public function serialize();
}
?>
