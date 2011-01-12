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
     * The header table is a quick lookup table for
     * a header by it's key
     * @var <array>
     */
    public $headerTable;

    /**
     * The place to keep the Message elements
     *
     * @var <array>
     */
    private $Messages;

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
        $this->Messages = array();
        $this->headerTable = array();
        $this->amfVersion = 0;
    }

    /**
     * addHeader places a new header into the pool of headers.
     *
     * Each header has 3 properties, they header key, the required flag
     * and the data associated with the header.
     *
     * @param object $header The AMFHeader object to add to the list
     */
    public function addHeader(&$header) {
            //$len = array_push($this->headers, $header);
            $this->headers[] = $header;
            $name = $header->name;
            $this->headerTable[$name] = $header;
    }

    /**
     * getHeader returns a header record for a given key
     *
     * @param string $key The header key
     * @return mixed The header record
     */
    public function getHeader ($key) {
            if (isset($this->headerTable[$key])) {
                    return $this->headerTable[$key];
            }
            return false;
    }

    /**
     * Gets the number of headers for this AMF packet
     *
     * @return int The header count
     */
    public function numHeaders() {
            return count($this->headers);
    }

    /**
     * Get the header at the specified position.
     *
     * If you pass an id this method will return the header
     * located at that id, otherwise it will return the first header
     *
     * @param int $id Optional id field
     * @return array The header object
     */
    public function getHeaderAt($id = 0) {
            return $this->headers[$id];
    }

    /**
     * addMessage has the job of adding a new Message element to the Messages array.
     *
     * @param string $t The target URI
     * @param string $r The response URI
     * @param mixed $v The value of the object
     * @param string $ty The type of the results
     * @param int $ps The pagesize of a recordset
     */
    public function addMessage($Message) {
            $this->Messages[] = $Message;
    }

    /**
     * addMessageAt provides an interface to push a Message element to a desired
     * position in the array.
     *
     * @param int $pos The position to add the Message element
     * @param AMFMessage $Message The Message element to add
     */
    public function addMessageAt($pos, $Message) {
            array_splice($this->Messages, $pos, 0, array($Message)); // splice the new Message into the array
    }

    /**
     * removeMessageAt provides an interface to remove a Message element to a desired
     * position in the array.
     *
     * @param int $pos The position to add the Message element
     * @param AMFMessage $Message The Message element to add
     */
    public function removeMessageAt($pos) {
            array_splice($this->Messages, $pos, 1); // splice the new Message into the array
    }

    /**
     * numMessage returns the total number of Message elements.  There is one Message
     * element for each method call.
     *
     * @return int The number of Message elements
     */
    public function numMessages() {
            return count($this->Messages);
    }

    /**
     * getMessageAt returns the current Message element the specified position.
     *
     * If a integer is passed this method will return the element at the given position.
     * Otherwise the first element will be returned.
     *
     * @param int $id The id of the Message element desired
     * @return array The Message element
     */
    public function getMessageAt($id = 0) {
            return $this->Messages[$id];
    }
}
?>
