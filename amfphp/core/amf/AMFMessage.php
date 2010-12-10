<?php
/**
 * content holder for an AMF Message.
 * TODO there is a confusion here between the AMF Message and the AMF Packets. This is probably because AMFPHP was written before the publication of the official specs. Clean up!
 *
 * @author Ariel Sommeria-klein
 */
class AMFMessage {
    /**
     * The place to keep the headers data
     *
     * @var <array>
     */
    private $headers;

    /**
     * The header table is a quick lookup table for
     * a header by it's key
     * @var <array>
     */
    private $headerTable;

    /**
     * The place to keep the body elements
     *
     * @var <array>
     */
    private $bodies;

    /**
     * either 0 or 3. This is stored here when deserializing, because the serializer needs the info
     * @var <int>
     */
    public $amfVersion;
    
    /**
     * The constructor function for a new AMF object.
     *
     * All the constructor does is initialize the headers and bodys containers
     */
    public function __construct() {
        $this->headers = array();
        $this->bodies = array();
        $this->headerTable = array();
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
     * addBody has the job of adding a new body element to the bodys array.
     *
     * @param string $t The target URI
     * @param string $r The response URI
     * @param mixed $v The value of the object
     * @param string $ty The type of the results
     * @param int $ps The pagesize of a recordset
     */
    public function addBody($body) {
            $this->bodies[] = $body;
    }

    /**
     * addBodyAt provides an interface to push a body element to a desired
     * position in the array.
     *
     * @param int $pos The position to add the body element
     * @param AMFBody $body The body element to add
     */
    public function addBodyAt($pos, $body) {
            array_splice($this->bodies, $pos, 0, array($body)); // splice the new body into the array
    }

    /**
     * removeBodyAt provides an interface to remove a body element to a desired
     * position in the array.
     *
     * @param int $pos The position to add the body element
     * @param AMFBody $body The body element to add
     */
    public function removeBodyAt($pos) {
            array_splice($this->bodies, $pos, 1); // splice the new body into the array
    }

    /**
     * numBody returns the total number of body elements.  There is one body
     * element for each method call.
     *
     * @return int The number of body elements
     */
    public function numBodies() {
            return count($this->bodies);
    }

    /**
     * getBodyAt returns the current body element the specified position.
     *
     * If a integer is passed this method will return the element at the given position.
     * Otherwise the first element will be returned.
     *
     * @param int $id The id of the body element desired
     * @return array The body element
     */
    public function getBodyAt($id = 0) {
            return $this->bodies[$id];
    }
}
?>
