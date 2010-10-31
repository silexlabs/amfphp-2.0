<?php
	require_once("util/MessageBody.php");
	require_once("util/MessageHeader.php");
	require_once("util/WrapperClasses.php");

	class AMFDeserializer
	{
		protected $raw_data;

		/**
		 * The number of bodies in the packet.
		 *
		 * @access private
		 * @var int
		 */
		protected $body_count;

		/**
		 * The current seek cursor of the stream
		 *
		 * @access private
		 * @var int
		 */
		protected $current_byte;

		/**
		 * The number of headers in the packet.
		 *
		 * @access private
		 * @var int
		 */
		protected $header_count;

		/**
		 * The content of the packet headers
		 *
		 * @access private
		 * @var string
		 */
		protected $headers = array();

		/**
		 * metaInfo
		 */
		protected $meta;

		protected $storedStrings;
		protected $storedObjects;
		protected $storedDefinitions;
		protected $amf0storedObjects;

		public $bodies = array();

		public function __construct($raw)
		{
			$this->raw_data = $raw;
			$this->current_byte = 0;
			$this->content_length = strlen($this->raw_data); // grab the total length of this stream
			$this->storedStrings = array();
			$this->storedObjects = array();
			$this->storedDefinitions = array();
			$this->decodeFlags = ($this->isBigEndian() * 2) | 4;
		}

		/**
		 * deserialize invokes this class to transform the raw data into valid object
		 *
		 * @param object $amfdata The object to put the deserialized data in
		 */
		public function deserialize() {
			$time = microtime(true);
			$this->readHeader(); // read the binary header
			$this->readBody(); // read the binary body
			if($this->decodeFlags & 1 == 1)
			{
				//AMF3 mode
				$GLOBALS['amfphp']['encoding'] = "amf3";
			}
			global $amfphp;
			$amfphp['decodeTime'] = microtime(true) - $time;
		}

		/**
		 * readHeader converts that header section of the amf message into php obects.
		 * Header information typically contains meta data about the message.
		 */
		protected function readHeader() {

			$topByte = $this->readByte(); // ignore the first two bytes --  version or something
			$secondByte = $this->readByte(); //0 for Flash,
											 //1 for FlashComm
			//Disable debug events for FlashComm
			$GLOBALS['amfphp']['isFlashComm'] = $secondByte == 1;

			//If firstByte != 0, then the AMF data is corrupted, for example the transmission
			//
			if(!($topByte == 0 || $topByte == 3))
			{
				trigger_error("Malformed AMF message, connection may have dropped");
				exit();
			}
			$this->header_count = $this->readInt(); //  find the total number of header elements

			while ($this->header_count--) { // loop over all of the header elements
				$name = $this->readUTF();
				$required = $this->readByte() == 1; // find the must understand flag
				//$length   = $this->readLong(); // grab the length of  the header element
				$this->current_byte += 4; // grab the length of the header element

				$type = $this->readByte();  // grab the type of the element
				$content = $this->readData($type); // turn the element into real data

				$this->headers[] = new MessageHeader($name, $required, $content); // save the name/value into the headers array
			}

		}

		/**
		 * Determine the endianness of the system
		 *
		 * @return bool - Big (true) or Little (false)
		 */
		private function isBigEndian()
		{
			$tmp = pack("d", 1);
			return $tmp == "\0\0\0\0\0\0\360\77";
		}

		protected function readBody()
		{
			$this->body_count = $this->readInt(); // find the total number  of body elements
			while ($this->body_count--)
			{ // loop over all of the body elements

				$this->amf0storedObjects = array();
				$this->storedStrings = array();
				$this->storedObjects = array();
				$this->storedDefinitions = array();

				$target = $this->readUTF();
				$response = $this->readUTF(); //    the response that the client understands

				//$length = $this->readLong(); // grab the length of    the body element
				$this->current_byte += 4;

				$type = $this->readByte(); // grab the type of the element
				$data = $this->readData($type); // turn the element into real data

				$this->bodies[] = new MessageBody($target, $response, $data); // add the body element to the body object

			}
		}

		/**
		 * readInt grabs the next 2 bytes and returns the next two bytes, shifted and combined
		 * to produce the resulting integer
		 *
		 * @return int The resulting integer from the next 2 bytes
		 */
		protected function readInt()
		{
			return ((ord($this->raw_data[$this->current_byte++]) << 8) |
				ord($this->raw_data[$this->current_byte++])); // read the next 2 bytes, shift and add
		}

		/**
		 * readUTF first grabs the next 2 bytes which represent the string length.
		 * Then it grabs the next (len) bytes of the resulting string.
		 *
		 * @return string The utf8 decoded string
		 */
		protected function readUTF()
		{
			$length = $this->readInt(); // get the length of the string (1st 2 bytes)
			//BUg fix:: if string is empty skip ahead
			if ($length == 0)
			{
				return "";
			}
			else
			{
				$val = substr($this->raw_data, $this->current_byte, $length); // grab the string
				$this->current_byte += $length; // move the seek head to the end of the string

				// TODO: Include charset transliteration
				return $val; // return the string
			}
		}

		/**
		 * readByte grabs the next byte from the data stream and returns it.
		 *
		 * @return int The next byte converted into an integer
		 */
		protected function readByte()
		{
			return ord($this->raw_data[$this->current_byte++]); // return the next byte
		}

		/**
		 * readData is the main switch for mapping a type code to an actual
		 * implementation for deciphering it.
		 *
		 * @param mixed $type The $type integer
		 * @return mixed The php version of the data in the message block
		 */
		public function readData($type)
		{
			switch ($type)
			{
				case 0: // number
					$data = $this->readDouble();
					break;
				case 1: // boolean
					$data = $this->readByte() == 1;
					break;
				case 2: // string
					$data = $this->readUTF();
					break;
				case 3: // object Object
					$data = $this->readObject();
					break;
				case 5: // null
					$data = null;
					break;
				case 6: // undefined
					$data = null;
					break;
				case 7: // Circular references are returned here
					$data = $this->readReference();
					break;
				case 8: // mixed array with numeric and string keys
					$data = $this->readMixedArray();
					break;
				case 10: // array
					$data = $this->readArray();
					break;
				case 11: // date
					$data = $this->readDate();
					break;
				case 12: // string, strlen(string) > 2^16
					$data = $this->readLongUTF();
					break;
				case 13: // mainly internal AS objects
					$data = null;
					break;
				case 15: // XML
					$data = $this->readLongUTF();
					break;
				case 16: // Custom Class
					$data = $this->readCustomClass();
					break;
				case 17: //AMF3-specific
					$GLOBALS['amfphp']['encoding'] = "amf3";
					$data = $this->readAmf3Data();
					break;
				default: // unknown case
					trigger_error("Found unhandled type with code: $type");
					exit();
					break;
			}
			return $data;
		}

		/**
		 * readDouble reads the floating point value from the bytes stream and properly orders
		 * the bytes depending on the system architecture.
		 *
		 * @return float The floating point value of the next 8 bytes
		 */
		protected function readDouble()
		{
			$bytes = substr($this->raw_data, $this->current_byte, 8);
			$this->current_byte += 8;
			if ($this->isBigEndian())
			{
				$bytes = strrev($bytes);
			}
			$zz = unpack("dflt", $bytes); // unpack the bytes
			return $zz['flt']; // return the number from the associative array
		}

		/**
		 * readObject reads the name/value properties of the amf message and converts them into
		 * their equivilent php representation
		 *
		 * @return array The php array with the object data
		 */
		protected function readObject()
		{
			$ret = array(); // init the array
			$this->amf0storedObjects[] = & $ret;
			$key = $this->readUTF(); // grab the key
			for ($type = $this->readByte(); $type != 9; $type = $this->readByte())
			{
				$val = $this->readData($type); // grab the value
				$ret[$key] = $val; // save the name/value pair in the array
				$key = $this->readUTF(); // get the next name
			}
			return $ret; // return the array
		}

		/**
		 * readReference replaces the old readFlushedSO. It treats where there
		 * are references to other objects. Currently it does not resolve the
		 * object as this would involve a serious amount of overhead, unless
		 * you have a genius idea
		 *
		 * @return String
		 */
		protected function readReference()
		{
			$reference = $this->readInt();
			return $this->amf0storedObjects[$reference];
		}

		/**
		 * readMixedArray turns an array with numeric and string indexes into a php array
		 *
		 * @return array The php array with mixed indexes
		 */
		protected function readMixedArray()
		{
			//$length   = $this->readLong(); // get the length  property set by flash
			$this->current_byte += 4;
			return $this->readMixedObject(); // return the body of mixed array
		}

		/**
		 * readMixedObject reads the name/value properties of the amf message and converts
		 * numeric looking keys to numeric keys
		 *
		 * @return array The php array with the object data
		 */
		protected function readMixedObject()
		{
			$ret = array(); // init the array
			$this->amf0storedObjects[] = & $ret;
			$key = $this->readUTF(); // grab the key
			for ($type = $this->readByte(); $type != 9; $type = $this->readByte())
			{
				$val = $this->readData($type); // grab the value
				if (is_numeric($key))
				{
					$key = (float) $key;
				}
				$ret[$key] = $val; // save the name/value pair in the array
				$key = $this->readUTF(); // get the next name
			}
			return $ret; // return the array
		}

		/**
		 * readArray turns an all numeric keyed actionscript array into a php array.
		 *
		 * @return array The php array
		 */
		protected function readArray()
		{
			$ret = array(); // init the array object
			$this->amf0storedObjects[] = & $ret;
			$length = $this->readLong(); // get the length  of the array
			for ($i = 0; $i < $length; $i++)
			{ // loop over all of the elements in the data
				$type = $this->readByte(); // grab the type for each element
				$ret[] = $this->readData($type); // grab each element
			}
			return $ret; // return the data

		}

		/**
		 * readDate reads a date from the amf message and returns the time in ms.
		 * This method is still under development.
		 *
		 * @return long The date in ms.
		 */
		protected function readDate()
		{
			$ms = $this->readDouble(); // date in milliseconds from 01/01/1970
			$int = $this->readInt(); // nasty way to get timezone
			if ($int > 720)
			{
				$int = -(65536 - $int);
			}
			$int *= -60;
			//$int *= 1000;
			//$min = $int % 60;
			//$timezone = "GMT " . - $hr . ":" . abs($min);
			// end nastiness

			//We store the last timezone found in date fields in the request
			//FOr most purposes, it's expected that the timezones
			//don't change from one date object to the other (they change per client though)
			//DateWrapper::setTimezone($int);
			// TODO: Create new DateWrapper
			return $ms;
		}

		/**
		 * readLongUTF first grabs the next 4 bytes which represent the string length.
		 * Then it grabs the next (len) bytes of the resulting in the string
		 *
		 * @return string The utf8 decoded string
		 */
		protected function readLongUTF()
		{
			$length = $this->readLong(); // get the length of the string (1st 4 bytes)
			$val = substr($this->raw_data, $this->current_byte, $length); // grab the string
			$this->current_byte += $length; // move the seek head to the end of the string

			// TODO: Include charset transliteration
			return $val; // return the string
		}

		/**
		 * readCustomClass reads the amf content associated with a class instance which was registered
		 * with Object.registerClass.  In order to preserve the class name an additional property is assigned
		 * to the object "_explicitType".  This property will be overwritten if it existed within the class already.
		 *
		 * @return object The php representation of the object
		 */
		protected function readCustomClass()
		{
			$typeIdentifier = str_replace('..', '', $this->readUTF());
			$obj = $this->mapClass($typeIdentifier);
			$isObject = true;
			if ($obj == NULL)
			{
				$obj = array();
				$isObject = false;
			}
			$this->amf0storedObjects[] = & $obj;
			$key = $this->readUTF(); // grab the key
			for ($type = $this->readByte(); $type != 9; $type = $this->readByte())
			{
				$val = $this->readData($type); // grab the value
				if ($isObject)
				{
					$obj->$key = $val; // save the name/value pair in the array
				}
				else
				{
					$obj[$key] = $val; // save the name/value pair in the array
				}
				$key = $this->readUTF(); // get the next name
			}
			if (!$isObject)
			{
				$obj['_explicitType'] = $typeIdentifier;
			}
			return $obj; // return the array
		}

		public function readAmf3Data()
		{
			$type = $this->readByte();
			switch ($type)
			{
				case 0x00 :
					return null; //undefined
				case 0x01 :
					return null; //null
				case 0x02 :
					return false; //boolean false
				case 0x03 :
					return true; //boolean true
				case 0x04 :
					return $this->readAmf3Int();
				case 0x05 :
					return $this->readDouble();
				case 0x06 :
					return $this->readAmf3String();
				case 0x07 :
					return $this->readAmf3XmlString();
				case 0x08 :
					return $this->readAmf3Date();
				case 0x09 :
					return $this->readAmf3Array();
				case 0x0A :
					return $this->readAmf3Object();
				case 0x0B :
					return $this->readAmf3XmlString();
				case 0x0C :
					return $this->readAmf3ByteArray();
				default:
					trigger_error("undefined Amf3 type encountered: " . $type, E_USER_ERROR);
			}
		}


		/**
		 * Handle decoding of the variable-length representation
		 * which gives seven bits of value per serialized byte by using the high-order bit
		 * of each byte as a continuation flag.
		 *
		 * @return read integer value
		 */

		protected function readAmf3Int()
		{
			$int = $this->readByte();
			if ($int < 128)
				return $int;
			else
			{
				$int = ($int & 0x7f) << 7;
				$tmp = $this->readByte();
				if ($tmp < 128)
				{
					return $int | $tmp;
				}
				else
				{
					$int = ($int | ($tmp & 0x7f)) << 7;
					$tmp = $this->readByte();
					if ($tmp < 128)
					{
						return $int | $tmp;
					}
					else
					{
						$int = ($int | ($tmp & 0x7f)) << 8;
						$tmp = $this->readByte();
						$int |= $tmp;

						// Integers in AMF3 are 29 bit. The MSB (of those 29 bit) is the sign bit.
						// In order to properly convert that integer to a PHP integer - the system
						// might be 32 bit, 64 bit, 128 bit or whatever - all higher bits need to
						// be set.

						if (($int & 0x10000000) !== 0)
						{
							$int |= ~0x1fffffff; // extend the sign bit regardless of integer (bit) size
						}
						return $int;
					}
				}
			}
		}

		protected function readAmf3Date()
		{
			$dateref = $this->readAmf3Int();
			if (($dateref & 0x01) == 0)
			{
				$dateref = $dateref >> 1;
				if ($dateref >= count($this->storedObjects))
				{
					trigger_error('Undefined date reference: ' . $dateref, E_USER_ERROR);
					return false;
				}
				return $this->storedObjects[$dateref];
			}
			//$timeOffset = ($dateref >> 1) * 6000 * -1;
			$ms = $this->readDouble();

			//$date = $ms-$timeOffset;
			$date = $ms;

			$this->storedObjects[] = & $date;
			return $date;
		}

		/**
		 * readString
		 *
		 * @return string
		 */
		protected function readAmf3String()
		{

			$strref = $this->readAmf3Int();

			if (($strref & 0x01) == 0)
			{
				$strref = $strref >> 1;
				if ($strref >= count($this->storedStrings))
				{
					trigger_error('Undefined string reference: ' . $strref, E_USER_ERROR);
					return false;
				}
				return $this->storedStrings[$strref];
			} else
			{
				$strlen = $strref >> 1;
				$str = "";
				if ($strlen > 0)
				{
					$str = $this->readBuffer($strlen);
					$this->storedStrings[] = $str;
				}
				return $str;
			}

		}

		protected function readAmf3XmlString()
		{
			$handle = $this->readAmf3Int();
			$inline = (($handle & 1) != 0);
			$handle = $handle >> 1;
			if ($inline)
			{
				$xml = $this->readBuffer($handle);
				$this->storedStrings[] = $xml;
			}
			else
			{
				$xml = $this->storedObjects[$handle];
			}
			return $xml;
		}

		protected function readAmf3ByteArray()
		{
			$handle = $this->readAmf3Int();
			$inline = (($handle & 1) != 0);
			$handle = $handle >> 1;
			if ($inline)
			{
				$ba = new ByteArray($this->readBuffer($handle));
				$this->storedObjects[] = $ba;
			}
			else
			{
				$ba = $this->storedObjects[$handle];
			}
			return $ba;
		}

		protected function readAmf3Array()
		{
			$handle = $this->readAmf3Int();
			$inline = (($handle & 1) != 0);
			$handle = $handle >> 1;
			if ($inline)
			{
				$hashtable = array();
				$this->storedObjects[] = & $hashtable;
				$key = $this->readAmf3String();
				while ($key != "")
				{
					$value = $this->readAmf3Data();
					$hashtable[$key] = $value;
					$key = $this->readAmf3String();
				}

				for ($i = 0; $i < $handle; $i++)
				{
					//Grab the type for each element.
					$value = $this->readAmf3Data();
					$hashtable[$i] = $value;
				}
				return $hashtable;
			}
			else
			{
				return $this->storedObjects[$handle];
			}
		}

		protected function readAmf3Object()
		{
			$handle = $this->readAmf3Int();
			$inline = (($handle & 1) != 0);
			$handle = $handle >> 1;

			if ($inline)
			{
				//an inline object
				$inlineClassDef = (($handle & 1) != 0);
				$handle = $handle >> 1;
				if ($inlineClassDef)
				{
					//inline class-def
					$typeIdentifier = $this->readAmf3String();
					$typedObject = !is_null($typeIdentifier) && $typeIdentifier != "";
					//flags that identify the way the object is serialized/deserialized
					$externalizable = (($handle & 1) != 0);
					$handle = $handle >> 1;
					$dynamic = (($handle & 1) != 0);
					$handle = $handle >> 1;
					$classMemberCount = $handle;

					$classMemberDefinitions = array();
					for ($i = 0; $i < $classMemberCount; $i++)
					{
						$classMemberDefinitions[] = $this->readAmf3String();
					}
					//string mappedTypeName = typeIdentifier;
					//if( applicationContext != null )
					//	mappedTypeName = applicationContext.GetMappedTypeName(typeIdentifier);

					$classDefinition = array("type" => $typeIdentifier, "members" => $classMemberDefinitions,
						"externalizable" => $externalizable, "dynamic" => $dynamic);
					$this->storedDefinitions[] = $classDefinition;
				}
				else
				{
					//a reference to a previously passed class-def
					$classDefinition = $this->storedDefinitions[$handle];
				}
			}
			else
			{
				//an object reference
				return $this->storedObjects[$handle];
			}


			$type = $classDefinition['type'];
			$obj = $this->mapClass($type);

			$isObject = true;
			if ($obj == NULL)
			{
				$obj = array();
				$isObject = false;
			}

			//Add to references as circular references may search for this object
			$this->storedObjects[] = & $obj;

			if ($classDefinition['externalizable'])
			{
				if ($type == 'flex.messaging.io.ArrayCollection')
				{
					$obj = $this->readAmf3Data();
				}
				else if ($type == 'flex.messaging.io.ObjectProxy')
				{
					$obj = $this->readAmf3Data();
				}
				else
				{
					trigger_error("Unable to read externalizable data type " . $type, E_USER_ERROR);
				}
			}
			else
			{
				$members = $classDefinition['members'];
				$memberCount = count($members);
				for ($i = 0; $i < $memberCount; $i++)
				{
					$val = $this->readAmf3Data();
					$key = $members[$i];
					if ($isObject)
					{
						$obj->$key = $val;
					}
					else
					{
						$obj[$key] = $val;
					}
				}

				if ($classDefinition['dynamic'] /* && obj is ASObject*/)
				{
					$key = $this->readAmf3String();
					while ($key != "")
					{
						$value = $this->readAmf3Data();
						if ($isObject)
						{
							$obj->$key = $value;
						}
						else
						{
							$obj[$key] = $value;
						}
						$key = $this->readAmf3String();
					}
				}

				if ($type != '' && !$isObject)
				{
					$obj['_explicitType'] = $type;
				}
			}

			if ($isObject && method_exists($obj, 'init'))
			{
				$obj->init();
			}

			return $obj;
		}

		/**
		 * readLong grabs the next 4 bytes shifts and combines them to produce an integer
		 *
		 * @return int The resulting integer from the next 4 bytes
		 */
		protected function readLong()
		{
			return ((ord($this->raw_data[$this->current_byte++]) << 24) |
				(ord($this->raw_data[$this->current_byte++]) << 16) |
				(ord($this->raw_data[$this->current_byte++]) << 8) |
				ord($this->raw_data[$this->current_byte++])); // read the next 4 bytes, shift and add
		}

		protected function mapClass($typeIdentifier)
		{
			//Check out if class exists
			if ($typeIdentifier == "")
			{
				return NULL;
			}
			$clazz = NULL;
			$mappedClass = str_replace('.', '/', $typeIdentifier);

			if ($typeIdentifier == "flex.messaging.messages.CommandMessage")
			{
				return new CommandMessage();
			}
			if ($typeIdentifier == "flex.messaging.messages.RemotingMessage")
			{
				return new RemotingMessage();
			}

			if (isset($GLOBALS['amfphp']['incomingClassMappings'][$typeIdentifier]))
			{
				$mappedClass = str_replace('.', '/', $GLOBALS['amfphp']['incomingClassMappings'][$typeIdentifier]);
			}

			$include = FALSE;
			if (file_exists($GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.php'))
			{
				$include = $GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.php';
			}
			elseif (file_exists($GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.class.php'))
			{
				$include = $GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.class.php';
			}

			if ($include !== FALSE)
			{
				include_once($include);
				$lastPlace = strrpos('/' . $mappedClass, '/');
				$classname = substr($mappedClass, $lastPlace);
				if (class_exists($classname))
				{
					$clazz = new $classname;
				}
			}

			return $clazz; // return the object
		}

		/**
		 * Taken from SabreAMF
		 */
		protected function readBuffer($len)
		{
			$data = "";
			for ($i = 0; $i < $len; $i++)
			{
				$data .= $this->raw_data
				{$i + $this->current_byte};
			}
			$this->current_byte += $len;
			return $data;
		}
	}
?>