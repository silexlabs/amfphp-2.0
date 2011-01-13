<?php
/**
 * content holder for an AMF Packet.
 * TODO there is a confusion here between the AMF Packet and the AMF Packets. This is probably because AMFPHP was written before the publication of the official specs. Clean up!
 *
 * @author Ariel Sommeria-klein
 */
class AMFPacket {
    /**
     * The place to keep the headers data
     *
     * @var <array>
     */
    public $headers;

    /**
     * The place to keep the Message elements
     *
     * @var <array>
     */
    public $messages;

    /**
     * either 0 or 3. This is stored here when deserializing, because the serializer needs the info
     * @var <int>
     */
    public $amfVersion;


    /**
     * The constructor function for a new AMF object.
     *
     * All the constructor does is initialize the headers and Messages containers
     */
    public function __construct() {
        $this->headers = array();
        $this->messages = array();
        $this->headerTable = array();
        $this->amfVersion = 0;
    }

    

}
?>
