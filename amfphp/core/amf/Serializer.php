<?php
/**
 * core_amf_Serializer manages the job of translating PHP objects into
 * the actionscript equivalent via AMF.  The main method of the serializer
 * is the serialize method which takes and AMFObject as it's argument
 * and builds the resulting AMF Message.
 * TODO spit into 2 classes, one for AMF0 , one for AMF3 or maybe more.
 * 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 AMFphp.org
 * @package flashservices
 * @subpackage io
 * @version $Id: Serializer.php,v 1.39 2005/07/22 10:58:11 pmineault Exp $
 */

class core_amf_Serializer implements core_common_ISerializer{

    /**
     *
     * @var <String> the output stream
     */
    private $outBuffer;
    
    /**
     *
     * @var <core_amf_Packet>
     */
    private $Packet;

    /**
     * the maximum amount of objects stored for reference
     */
    const MAX_STORED_OBJECTS = 1024;
    /**
     *
     * used for AMF0 references
     * @var <array>
     */
    private $AMF0StoredObjects;

    /**
     *
     * used for AMF3 references
     * @var <array>
     */
    private $storedObjects;

    /**
     * amf3 references to strings
     * @var <array>
     */
    private  $storedStrings;
    /**
     * Count the number of unique sent strings.
     * The number is used as reference in case an already
     * sent string should be sent again.
     *
     * @var <int>
     */

     private $encounteredStrings;


    /**
     *
     * @param <core_amf_Packet> $packet
     */
    public function __construct($packet){
        $this->packet = $packet;
        $this->AMF0StoredObjects = array();
        $this->storedObjects = array();
        $this->storedStrings = array();
        $this->encounteredStrings = 0;
    }
    
    /**
     * serializes the Packet passed in the constructor
     * TODO clean up the mess with the temp buffers. A.S.
     */
    public function serialize(){
        $this->writeInt(0); //  write the version (always 0)
        $count = count($this->packet->headers);
        $this->writeInt($count); // write header count
        for ($i = 0; $i < $count; $i++) {
                //write headers
                $header = $this->packet->headers[$i];
                $this->writeUTF($header->name);
                if($header->required){
                    $this->writeByte(1);
                }else{
                    $this->writeByte(0);
                }
                $tempBuf = $this->outBuffer;
                $this->outBuffer = "";
                $this->writeData($header->value);
                $serializedHeader = $this->outBuffer;
                $this->outBuffer = $tempBuf;
                $this->writeLong(strlen($serializedHeader));
                $this->outBuffer .= $serializedHeader;
        }
        $count = count($this->packet->messages);
        $this->writeInt($count); // write the Message  count
        for ($i = 0; $i < $count; $i++) {
                //write Message
                $this->AMF0StoredObjects = array();
                $this->storedStrings = array();
                $this->storedObjects = array();
                $this->encounteredStrings = 0;
                $this->storedDefinitions = 0;
                $message = $this->packet->messages[$i];
                $this->currentMessage = & $message;
                $this->writeUTF($message->targetURI);
                $this->writeUTF($message->responseURI); 
                //save the current buffer, and flush it to write the Message
                $tempBuf = $this->outBuffer;
                $this->outBuffer = "";
                $this->writeData($message->data);
                $serializedMessage = $this->outBuffer;
                $this->outBuffer = $tempBuf;
                $this->writeLong(strlen($serializedMessage));
                $this->outBuffer .= $serializedMessage;
        }

        return $this->outBuffer;

    }

    public function getOutput(){
        return $this->outBuffer;
    }
    /**
     * writeByte writes a singe byte to the output stream
     * 0-255 range
     *
     * @param int $b An int that can be converted to a byte
     */
    protected function writeByte($b) {
            $this->outBuffer .= pack("c", $b); // use pack with the c flag
    }

    /**
     * writeInt takes an int and writes it as 2 bytes to the output stream
     * 0-65535 range
     *
     * @param int $n An integer to convert to a 2 byte binary string
     */
    protected function writeInt($n) {
            $this->outBuffer .= pack("n", $n); // use pack with the n flag
    }

    /**
     * writeLong takes an int, float or double and converts it to a 4 byte binary string and
     * adds it to the output buffer
     *
     * @param long $l A long to convert to a 4 byte binary string
     */
    protected function writeLong($l) {
            $this->outBuffer .= pack("N", $l); // use pack with the N flag
    }


    /**
     * writeDouble takes a float as the input and writes it to the output stream.
     * Then if the system is big-endian, it reverses the bytes order because all
     * doubles passed via remoting are passed little-endian.
     *
     * @param double $d The double to add to the output buffer
     */
    protected function writeDouble($d) {
            $b = pack("d", $d); // pack the bytes
            if (core_amf_Util::isSystemBigEndian()) { // if we are a big-endian processor
                    $r = strrev($b);
            } else { // add the bytes to the output
                    $r = $b;
            }

            $this->outBuffer .= $r;
    }

    /**
     * writeUTF takes and input string, writes the length as an int and then
     * appends the string to the output buffer
     *
     * @param string $s The string less than 65535 characters to add to the stream
     */
    protected function writeUtf($s) {
            $this->writeInt(strlen($s)); // write the string length - max 65535
            $this->outBuffer .= $s; // write the string chars
    }

    /**
     * writeLongUTF will write a string longer than 65535 characters.
     * It works exactly as writeUTF does except uses a long for the length
     * flag.
     *
     * @param string $s A string to add to the byte stream
     */
    protected function writeLongUtf($s) {
            $this->writeLong(strlen($s));
            $this->outBuffer .= $s; // write the string chars
    }

    /**
     * writeBoolean writes the boolean code (0x01) and the data to the output stream
     *
     * @param bool $d The boolean value
     */

    protected function writeBoolean($d) {
            $this->writeByte(1); // write the "boolean-marker"
            $this->writeByte($d); // write the boolean byte (0 = FALSE; rest = TRUE)
    }


    /**
     * writeString writes the string code (0x02) and the UTF8 encoded
     * string to the output stream.
     * Note: strings are truncated to 64k max length. Use XML as type
     * to send longer strings
     *
     * @param string $d The string data
     */
    protected function writeString($d) {
            $count = strlen($d);
            if($count < 65536)
            {
                    $this->writeByte(2);
                    $this->writeUTF($d);
            }
            else
            {
                    $this->writeByte(12);
                    $this->writeLongUTF($d);
            }
    }

    /**
     * writeXML writes the xml code (0x0F) and the XML string to the output stream
     * Note: strips whitespace
     * @param string $d The XML string
     */
    protected function writeXML($d) {
            if(!$this->writeReferenceIfExists($d))
            {
                    $this->writeByte(15);
                    $this->writeLongUTF(preg_replace('/\>(\n|\r|\r\n| |\t)*\</','><',trim($d)));
            }
    }

    /**
     * writeData writes the date code (0x0B) and the date value to the output stream
     *
     * @param date $d The date value
     */
    protected function writeDate($d) {
            $this->writeByte(11); // write  date code
            $this->writeDouble($d); //  write date (milliseconds from 1970)
            /**
             * write timezone
             * ?? this is wierd -- put what you like and it pumps it back into flash at the current GMT ??
             * have a look at the AMF it creates...
             */
            $this->writeInt(0);
    }

    /**
     * writeNumber writes the number code (0x00) and the numeric data to the output stream
     * All numbers passed through remoting are floats.
     *
     * @param int $d The numeric data
     */
    protected function writeNumber($d) {
            $this->writeByte(0); // write the number code
            $this->writeDouble(floatval($d)); // write  the number as a double
    }

    /**
     * writeNull writes the null code (0x05) to the output stream
     */
    protected function writeNull() {
            $this->writeByte(5); // null is only a  0x05 flag
    }


    /**
     * writeUndefined writes the Undefined code (0x06) to the output stream
     */
    protected function writeUndefined() {
            $this->writeByte(6); // Undefined is only a  0x06 flag
    }

    /**
     * writeObjectEnd writes the object end code (0x009) to the output stream
     */
    protected function writeObjectEnd() {
            $this->writeInt(0); //  write the end object flag 0x00, 0x00, 0x09
            $this->writeByte(9);
    }

    /**
     * writeArray first deterines if the PHP array contains all numeric indexes
     * or a mix of keys.  Then it either writes the array code (0x0A) or the
     * object code (0x03) and then the associated data.
     *
     * @param array $d The php array
     */
    protected function writeArray($d)
    {
            if($this->writeReferenceIfExists($d))
            {
                    return;
            }

            $numeric = array(); // holder to store the numeric keys
            $string = array(); // holder to store the string keys
            $len = count($d); // get the total number of entries for the array
            $largestKey = -1;
            foreach($d as $key => $data) { // loop over each element
                    if (is_int($key) && ($key >= 0)) { // make sure the keys are numeric
                            $numeric[$key] = $data; // The key is an index in an array
                            $largestKey = max($largestKey, $key);
                    } else {
                            $string[$key] = $data; // The key is a property of an object
                    }
            }
            $num_count = count($numeric); // get the number of numeric keys
            $str_count = count($string); // get the number of string keys

            if ( ($num_count > 0 && $str_count > 0) ||
                     ($num_count > 0 && $largestKey != $num_count - 1)) { // this is a mixed array

                    $this->writeByte(8); // write the mixed array code
                    $this->writeLong($num_count); // write  the count of items in the array
                    $this->writeObjectFromArray($numeric + $string); // write the numeric and string keys in the mixed array
            } else if ($num_count > 0) { // this is just an array

                    $num_count = count($numeric); // get the new count

                    $this->writeByte(10); // write  the array code
                    $this->writeLong($num_count); // write  the count of items in the array
                    for($i = 0 ; $i < $num_count ; $i++) { // write all of the array elements
                            $this->writeData($numeric[$i]);
                    }
            } else if($str_count > 0) { // this is an object
                    $this->writeByte(3); // this is an  object so write the object code
                    $this->writeObjectFromArray($string); // write the object name/value pairs
            } else { //Patch submitted by Jason Justman

                    $this->writeByte(10); // make this  an array still
                    $this->writeInt(0); //  give it 0 elements
                    $this->writeInt(0); //  give it an element pad, this looks like a bug in Flash,
                                                                                    //but keeps the next alignment proper
            }
    }

    protected function writeReferenceIfExists($d)
    {
            if(count($this->AMF0StoredObjects) >= self::MAX_STORED_OBJECTS)
            {
                    return false;
            }
            if(is_array($d))
            {
                    $this->AMF0StoredObjects[] = "";
                    return false;
            }
            if(($key = array_search($d, $this->AMF0StoredObjects, true)) !== FALSE)
            {
                    $this->writeReference($key);
                    return true;
            }
            else
            {
                    $this->AMF0StoredObjects[] = & $d;
                    return false;
            }
    }

    protected function writeReference($num)
    {
            $this->writeByte(0x07);
            $this->writeInt($num);
    }

    /**
     * Write a plain numeric array without anything fancy
     */
    protected function writePlainArray($d)
    {
            if(!$this->writeReferenceIfExists($d))
            {
                    $num_count = count($d);
                    $this->writeByte(10); // write  the mixed array code
                    $this->writeLong($num_count); // write  the count of items in the array
                    for($i = 0 ; $i < $num_count ; $i++) { // write all of the array elements
                            $this->writeData($d[$i]);
                    }
            }
    }

    /**
     * writeObjectFromArray handles writing a php array with string or mixed keys.  It does
     * not write the object code as that is handled by the writeArray and this method
     * is shared with the CustomClass writer which doesn't use the object code.
     *
     * @param array $d The php array with string keys
     */
    protected function writeObjectFromArray($d) {
            foreach($d as $key => $data) { // loop over each element
                    $this->writeUTF($key);  // write the name of the object
                    $this->writeData($data); // write the value of the object
            }
            $this->writeObjectEnd();
    }

    /**
     * writeObject handles writing a php array with string or mixed keys.  It does
     * not write the object code as that is handled by the writeArray and this method
     * is shared with the CustomClass writer which doesn't use the object code.
     *
     * @param array $d The php array with string keys
     */
    protected function writeAnonymousObject($d) {
            if(!$this->writeReferenceIfExists($d))
            {
                    $this->writeByte(3);
                    $objVars = (array) $d;
                    foreach($d as $key => $data) { // loop over each element
                            if($key[0] != "\0")
                            {
                                    $this->writeUTF($key);  // write the name of the object
                                    $this->writeData($data); // write the value of the object
                            }
                    }
                    $this->writeObjectEnd();
            }
    }


    /**
     * writeTypedObject takes an instance of a class and writes the variables defined
     * in it to the output stream.
     * To accomplish this we just blanket grab all of the object vars with get_object_vars, minus the _explicitType, whiuch is used as class name
     *
     * @param object $d The object to serialize the properties. The deserializer looks for core_amf_Constants::FIELD_EXPLICIT_TYPE on this object and writes it as the class name.
     */
    protected function writeTypedObject($d, $className) {
            if($this->writeReferenceIfExists($d))
            {
                    return;
            }

            $this->writeByte(16); // write  the custom class code

            $className = $d[core_amf_Constants::FIELD_EXPLICIT_TYPE];
            if(!$className){
                throw new Exception("_explicitType not found on a object that is to be sent as typed. " . print_r($d, true));
            }
            unset ($d[core_amf_Constants::FIELD_EXPLICIT_TYPE]);
            $this->writeUTF($className); // write the class name
            $objVars = $d;
            foreach($objVars as $key => $data) { // loop over each element
                    if($key[0] != "\0")
                    {
                            $this->writeUTF($key);  // write the name of the object
                            $this->writeData($data); // write the value of the object
                    }
            }
            $this->writeObjectEnd();
    }


    /**
     * writeData checks to see if the type was declared and then either
     * auto negotiates the type or relies on the user defined type to
     * serialize the data into AMF
     *
     * @param mixed $d The data
     */
    protected function writeData($d) {
            if ($this->packet->amfVersion == 3)
            {
                    $this->writeByte(0x11);
                    $this->writeAMF3Data($d);
                    return;
            }
            elseif (is_int($d) || is_float($d))
            { // double
                    $this->writeNumber($d);
                    return;
            }
            elseif (is_string($d))
            { // string
                    $this->writeString($d);
                    return;
            }
            elseif (is_bool($d))
            { // boolean
                    $this->writeBoolean($d);
                    return;
            }
            elseif (is_null($d))
            { // null
                    $this->writeNull();
                    return;
            }
            elseif (is_array($d))
            { // array
                    $this->writeArray($d);
                    return;
            }
            elseif (is_object($d))
            {
                    $className = strtolower(get_class($d));
                    if($className == 'domdocument')
                    {
                            $this->writeXML($d->saveXml());
                            return;
                    }
                    elseif($className == "simplexmlelement")
                    {
                            $this->writeXML($d->asXML());
                            return;
                    }
                    elseif($className == "undefined")
                    {
                            $this->writeUndefined();
                            return;
                    }
                    else if($className == 'stdclass' && !isset($d->_explicitType))
                    {
                            $this->writeAnonymousObject($d);
                            return;
                    }
                    //Fix for PHP5 overriden ArrayAccess and ArrayObjects with an explcit type 
                    //TODO not sure if this is still relevant. A.S.
                    elseif( (is_a($d, 'ArrayAccess') || is_a($d, 'ArrayObject')) && !isset($d->_explicitType))
                    {
                            $this->writeArray($d);
                            return;
                    }
                    else if(isset($d->_explicitType))
                    {
                            $type = $d->_explicitType;
                            unset ($d->_explicitType);
                            $this->writeTypedObject($d, $type);
                            return;
                    }else{
                            $this->writeArray($d);
                            return;
                    }
            }
            throw new Exception("couldn't write data ");
    }


	/********************************************************************************
	 *                             AMF3 related code
	 *******************************************************************************/

	/**
	 * @todo Is the reference still needed? PHP4 needed it for objects, but PHP5 always
	 * passes objects by reference. And PHP5 uses a copy-on-write approach, so that all
	 * values are passed as "reference", in case no changes take place.
         *
         * @todo no type markers ("\6", for example) in this method!
	 */

	protected function writeAmf3Data(& $d)
	{
		if (is_int($d))
		{ //int
			$this->writeAmf3Number($d);
			return;
		}
		elseif(is_float($d))
		{ //double
			$this->outBuffer .= "\5";
			$this->writeDouble($d);
			return;
		}
		elseif (is_string($d))
		{ // string
			$this->outBuffer .= "\6";
			$this->writeAmf3String($d);
			return;
		}
		elseif (is_bool($d))
		{ // boolean
			$this->writeAmf3Bool($d);
			return;
		}
		elseif (is_null($d))
		{ // null
			$this->writeAmf3Null();
			return;
		}
		elseif (is_array($d) && !isset($d->_explicitType))
		{ // array
			$this->writeAmf3Array($d);
			return;
		}
		elseif (is_resource($d))
		{ // resource
			$type = get_resource_type($d);
			list($type, $subtype) = $this->sanitizeType($type);
		}
		elseif (is_object($d))
		{
			$className = strtolower(get_class($d));
                        if($className == 'domdocument')
			{
				$this->writeAmf3Xml($d->saveXml());
				return;
			}
			elseif($className == "simplexmlelement")
			{
				$this->writeAmf3Xml($d->asXML());
				return;
			}
			elseif($className == 'bytearray')
			{
				$this->writeAmf3ByteArray($d->data);
				return;
			}
			// Fix for PHP5 overriden ArrayAccess and ArrayObjects with an explcit type
			elseif( (is_a($d, 'ArrayAccess') || is_a($d, 'ArrayObject')) && !isset($d->_explicitType))
			{
				$this->writeAmf3Array($d, true);
				return;
			}
			else
			{
				$this->writeAmf3Object($d);
				return;
			}
		}
                throw new Exception("couldn't write object " . print_r($d, false));
	}


	/**
	 * Write an ArrayCollection
	 */
	protected function writeAmf3ArrayCollectionPreamble()
	{
		$this->writeByte(0x0a);
		$this->writeByte(0x07);
		$this->writeAmf3String("flex.messaging.io.ArrayCollection");
		$this->storedDefinitions++;
		$this->storedObjects[] = "";
	}


	/**
	 * Write undefined (AMF3).
	 *
	 * @return nothing
	 */

	protected function writeAmf3Undefined()
	{
		$this->outBuffer .= "\0";
	}

	/**
	 * Write NULL (AMF3).
	 *
	 * @return nothing
	 */

	protected function writeAmf3Null()
	{
		$this->outBuffer .= "\1";
	}


	/**
	 * Write a boolean (AMF3).
	 *
	 * @param bool $d the boolean to serialise
	 *
	 * @return nothing
	 */

	protected function writeAmf3Bool($d)
	{
		$this->outBuffer .= $d ? "\3" : "\2";
	}


	/**
	 * Write an (un-)signed integer (AMF3).
	 *
	 * @see getAmf3Int()
	 *
	 * @param int $d the integer to serialise
	 *
	 * @return nothing
	 */

	protected function writeAmf3Int($d)
	{
		$this->outBuffer .= $this->getAmf3Int($d);
	}


	/**
	 * Write a string (AMF3). Strings are stored in a cache and in case the same string
	 * is written again, a reference to the string is sent instead of the string itself.
	 *
	 * @note Sending strings larger than 268435455 (2^28-1 byte) will (silently) fail!
	 *
	 * @note The string marker is NOT sent here and has to be sent before, if needed.
         *
	 *
	 * @param string $d the string to send
	 *
	 * @return The reference index inside the lookup table is returned. In case of an empty
	 * string which is sent in a special way, NULL is returned.
	 */

	protected function writeAmf3String($d)
	{

		/**
		 * @todo This method is writes a string, so the argument is expected to be a string!
		 * Add an is_string() check and throw an exception and then fix the errors. For now,
		 * the given value is casted into a string to "work around" the issues.
		 */

		$d = (string) $d;

		if( $d === '' )
		{
			//Write 0x01 to specify the empty string ("UTF-8-empty")
			$this->outBuffer .= "\1";
			return NULL;
		}
		else
		{
			if( !isset($this->storedStrings[$d]))
			{

				// The string is not yet available in the reference lookup table.
				// If the string is shorter than 64 byte, add it to the lookup cache;
				// if it is longer, do not store it locally. This way, it cannot be
				// referenced once it is encountered again. However, the AMF client
				// builds the reference lookup table as well, so in all cases this
				// string must increment the reference lookup table index.

				// The whole purpose of not storing long strings in PHP is to
				// save memory (in PHP script), as long strings are likely not to occur again.

				if(strlen($d) < 64)
				{
					$this->storedStrings[$d] = $this->encounteredStrings;
				}

				// In case transliteration should take place, the original
				// string is stored in the reference lookup table, but the
				// transliterated string is sent. This is no issue as further
				// occurrences of the to be transliterated string are sent
				// as references.
/*
//no longer valid here, there is no charset handler anymore. @todo clean this when charset plugin done
				if(!$raw)
				{
					$d = $this->charsetHandler->transliterate($d);
				}
*/
				$this->writeAmf3Int(strlen($d) << 1 | 1); // U29S-value
				$this->outBuffer .= $d;
				return $this->encounteredStrings++; // return the "previous" value
			}
			else
			{
				$key = $this->storedStrings[$d];
				$this->writeAmf3Int($key << 1); // U29S-ref
				return $key;
			}
		}
	}


	protected function writeAmf3Array(/* array */ $d, $arrayCollectionable = false)
	{
		//Circular referencing is disabled in arrays
		//Because if the array contains only primitive values,
		//Then === will say that the two arrays are strictly equal
		//if they contain the same values, even if they are really distinct
		//if(($key = array_search($d, $this->storedObjects, TRUE)) === FALSE )
		//{
			if(count($this->storedObjects) < self::MAX_STORED_OBJECTS)
			{
				$this->storedObjects[] = & $d;
			}

			$numeric = array(); // holder to store the numeric keys >= 0
			$string = array(); // holder to store the string keys; actually, non-integer or integer < 0 are stored
			$len = count($d); // get the total number of entries for the array
			$largestKey = -1;
			foreach($d as $key => $data) { // loop over each element
				if (is_int($key) && ($key >= 0)) { // make sure the keys are numeric
					$numeric[$key] = $data; // The key is an index in an array
					$largestKey = max($largestKey, $key);
				} else {
					$string[$key] = $data; // The key is a property of an object
				}
			}

			$num_count = count($numeric); // get the number of numeric keys
			$str_count = count($string); // get the number of string keys

			if (
				($str_count > 0 && $num_count == 0)  || // Only strings or negative integer keys are present.
				($num_count > 0 && $largestKey != $num_count - 1) // Non-negative integer keys are present, but the array is not "dense" (it has gaps).
			) { // this is a mixed array
				$this->writeAmf3ObjectFromArray($numeric + $string); // write the numeric and string keys in the mixed array
			} else { // this is just an array
				if($arrayCollectionable)
				{
					$this->writeAmf3ArrayCollectionPreamble();
				}

				$num_count = count($numeric);

				$this->outBuffer .= "\11";
				$handle = $num_count * 2 + 1;
				$this->writeAmf3Int($handle);

				foreach($string as $key => $val)
				{
					$this->writeAmf3String($key);
					$this->writeAmf3Data($val);
				}
				$this->writeAmf3String(""); //End start hash

				for($i = 0; $i < $num_count; $i++)
				{
					$this->writeAmf3Data($numeric[$i]);
				}
			}
		//}
		//else
		//{
		//	$handle = $key << 1;
		//	$this->outBuffer .= "\11";
		//	$this->writeAmf3Int($handle);
		//}
	}


	/**
	 * Serialise the array as if it is an object.
	 *
	 * @param array $d the array to serialise
	 *
	 * @return nothing
	 */

	protected function writeAmf3ObjectFromArray(/* array */ $d)
	{
		//Type this as a dynamic object
		$this->outBuffer .= "\12\13\1";

		foreach($d as $key => $val)
		{
			$this->writeAmf3String($key);
			$this->writeAmf3Data($val);
		}

		//Now we close the open object
		$this->outBuffer .= "\1";
	}

        //TODO this is commented, so probably wrong. Fix it! A.S.
	/*
	protected  void WriteAMF3DateTime(DateTime value)
	{
		if( !_objectReferences.Contains(value) )
		{
			_objectReferences.Add(value, _objectReferences.Count);
			int handle = 1;
			WriteAMF3IntegerData(handle);

			// Write date (milliseconds from 1970).
			DateTime timeStart = new DateTime(1970, 1, 1, 0, 0, 0);

			string timezoneCompensation = System.Configuration.ConfigurationSettings.AppSettings["timezoneCompensation"];
			if( timezoneCompensation != null && ( timezoneCompensation.ToLower() == "auto" ) )
			{
				value = value.ToUniversalTime();
			}

			TimeSpan span = value.Subtract(timeStart);
			long milliSeconds = (long)span.TotalMilliseconds;
			long date = BitConverter.DoubleToInt64Bits((double)milliSeconds);
			this.WriteLong(date);
		}
		else
		{
			int handle = (int)_objectReferences[value];
			handle = handle << 1;
			WriteAMF3IntegerData(handle);
		}
	}
	*/


	/**
	 * Return the serialisation of the given integer (AMF3).
	 *
	 * @note There does not seem to be a way to distinguish between signed and unsigned integers.
	 * This method just sends the lowest 29 bit as-is, and the receiver is responsible to interpret
	 * the result as signed or unsigned based on some context.
	 *
	 * @note The limit imposed by AMF3 is 29 bit. So in case the given integer is longer than 29 bit,
	 * only the lowest 29 bits will be serialised. No error will be logged!
	 * TODO refactor into writeAmf3Int
         * 
	 * @param int $d the integer to serialise
	 *
	 * @return string
	 */

	protected function getAmf3Int($d)
	{

		/**
		 * @todo The lowest 29 bits are kept and all upper bits are removed. In case of
		 * an integer larger than 29 bits (32 bit, 64 bit, etc.) the value will effectively change! Maybe throw an exception!
		 */

		$d &= 0x1fffffff;

		if($d < 0x80)
		{
			return
				chr($d);
		}
		elseif($d < 0x4000)
		{
			return
				chr($d >> 7 & 0x7f | 0x80) .
				chr($d & 0x7f);
		}
		elseif($d < 0x200000)
		{
			return
				chr($d >> 14 & 0x7f | 0x80) .
				chr($d >> 7 & 0x7f | 0x80) .
				chr($d & 0x7f);
		}
		else
		{
			return
				chr($d >> 22 & 0x7f | 0x80) .
				chr($d >> 15 & 0x7f | 0x80) .
				chr($d >> 8 & 0x7f | 0x80) .
				chr($d & 0xff);
		}
	}

	protected function writeAmf3Number($d)
	{
		if(is_int($d) && $d >= -268435456 && $d <= 268435455)//check valid range for 29bits
		{
			$this->outBuffer .= "\4";
			$this->writeAmf3Int($d);
		}
		else
		{
			//overflow condition would occur upon int conversion
			$this->outBuffer .= "\5";
			$this->writeDouble($d);
		}
	}

        //in amf3 there are 2 xml types, XMLDocument and XML. the amfphp deserializer parses them both to a String.
        //However, the serializer expects a real xml document
        //TODO fix these inconsistencies A.S.

	protected function writeAmf3Xml($d)
	{
		$d = preg_replace('/\>(\n|\r|\r\n| |\t)*\</','><',trim($d));
		$this->writeByte(0x07);
		$this->writeAmf3String($d);
	}

	protected function writeAmf3ByteArray($d)
	{
		$this->writeByte(0x0C);
		//this seems wrong... A.S.
                //$this->writeAmf3String($d, true);
		$this->writeAmf3ByteArrayMessage($d);
	}


	protected function writeAmf3ByteArrayMessage($d)
	{
		if( ($key = array_search($d, $this->storedObjects, TRUE)) === FALSE && $key === FALSE )
		{
			if(count($this->storedObjects) < self::MAX_STORED_OBJECTS)
			{
				$this->storedObjects[] = & $d;
			}
			$this->storedDefinitions++;
			$obj_length = strlen( $d );
			$this->writeAmf3Int( $obj_length << 1 | 0x01 );
			$this->outBuffer .= $d;
		} else {
			$handle = $key << 1;
			$this->writeAmf3Int($handle);
		}
	}

        /**
         * @TODO better support for traits. Right now the object is considered dynamic, and that's all
         * @param <mixed> $d
         */
	protected function writeAmf3Object($d)
	{
		//Write the object tag
		$this->outBuffer .= "\12";
		if( ($key = array_search($d, $this->storedObjects, TRUE)) === FALSE && $key === FALSE)
		{
			if(count($this->storedObjects) < self::MAX_STORED_OBJECTS)
			{
				$this->storedObjects[] = & $d;
			}

			$this->storedDefinitions++;

			$realObj = array();
			foreach($d as $key => $val)
			{
				if($key[0] != "\0" && $key != '_explicitType') //Don't show private members
				{
					$realObj[$key] = $val;
				}
			}

			//Type this as a dynamic object
			$this->outBuffer .= "\13";

			$className = $d[core_amf_Constants::FIELD_EXPLICIT_TYPE];

			$this->writeAmf3String($className);
                        
                        foreach($realObj as $key => $val)
			{
				$this->writeAmf3String($key);
				$this->writeAmf3Data($val);
			}
			//Now we close the open object
			$this->outBuffer .= "\1";
		}
		else
		{
			$handle = $key << 1;
			$this->writeAmf3Int($handle);
		}
	}



}
?>