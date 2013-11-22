<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_TestData
 */


/**
 * See AmfTestData for details, this is the extension for Amf3
 *
 * @package Tests_TestData
 * @author Ariel Sommeria-klein, based on work by ci-dev
 */
class Amf3TestData {
    /**
     * serialized undefined
     * @var string 
     */
    public $sUndefined;
    /**
     * undefined
     * @var Amfphp_Core_Amf_Types_Undefined 
     */
    public $dUndefined;
    /**
     * null
     * @var string 
     */
    public $sNull;
    /**
     * false
     * @var string 
     */
    public $sFalse;
    /**
     * true
     * @var string 
     */
    public $sTrue;
    /**
     * int 1
     * @var int 
     */
    public $dInt1;
    /**
     * int 2
     * @var string 
     */
    public $sInt1;
    /**
     * int 2
     * @var int 
     */
    public $dInt2;   
    
    /**
     * int 2
     * @var string 
     */
    public $sInt2;
    
    /**
     * double
     * @var float 
     */
    public $dDouble;
    
    /**
     * double
     * @var string 
     */    
    public $sDouble;

    /**
     * string
     * @var string
     */       
    public $dString;
    
    /**
     * string
     * @var string 
     */   
    public $sString;
    
    /**
     * string twice. dString serialized twice. test for reference
     * @var string 
     */   
    public $sStringTwice; 

    /**
     * long string
     * @var string 
     */   
    public $dLongString;
    
    /**
     * long string
     * @var string 
     */   
    public $sLongString;

    /**
     * empty string
     * @var string 
     */       
    public $dEmptyString;

    /**
     * empty string
     * @var string 
     */   
    public $sEmptyString;

    /**
     * xml document
     * @var Amfphp_Core_Amf_Types_XmlDocument
     */       
    public $dXmlDocument;

    /**
     * xml document
     * @var string 
     */   
    public $sXmlDocument;

    /**
     * xml
     * @var Amfphp_Core_Amf_Types_Xml 
     */       
    public $dXml;

    /**
     * xml
     * @var string 
     */   
    public $sXml;

    /**
     * date
     * @var Amfphp_Core_Amf_Types_Date
     */       
    public $dDate;

    /**
     * date
     * @var string 
     */       
    public $sDate;

    /**
     * empty array
     * @var array
     */       
    public $dEmptyArray;

    /**
     * empty array
     * @var string 
     */   
    public $sEmptyArray;

    /**
     * dense array
     * @var array
     */       
    public $dDenseArray;

    /**
     * dense array
     * @var string 
     */   
    public $sDenseArray;

    /**
     * mixed array
     * @var array
     */       
    public $dMixedArray;

    /**
     * mixed array
     * @var string 
     */   
    public $sMixedArray;

    /**
     * object
     * @var object
     */       
    public $dObject;

    /**
     * object
     * @var string 
     */   
    public $sObject;

    /**
     * byte array
     * @var Amfphp_Core_Amf_Types_ByteArray
     */       
    public $dByteArray;

    /**
     * byte array
     * @var string 
     */   
    public $sByteArray;
    
    /**
     * vector int
     * @var Amfphp_Core_Amf_Types_Vector
     */       
    public $dVectorInt;

    /**
     * vector int
     * @var string 
     */   
    public $sVectorInt;
    
    /**
     * vector uint
     * @var Amfphp_Core_Amf_Types_Vector
     */       
    public $dVectorUint;

    /**
     * vector uint
     * @var string 
     */   
    public $sVectorUint;
    
    /**
     * vector double
     * @var Amfphp_Core_Amf_Types_Vector
     */       
    public $dVectorDouble;

    /**
     * vector double
     * @var string 
     */   
    public $sVectorDouble;
    
    /**
     * vector object
     * @var Amfphp_Core_Amf_Types_Vector
     */       
    public $dVectorObject;

    /**
     * vector object
     * @var string 
     */   
    public $sVectorObject;
    
    /**
     * dictionary
     * @var Amfphp_Core_Amf_Types_Dictionary
     */       
    public $dDictionary;

    /**
     * dictionary
     * @var string 
     */   
    public $sDictionary;
    
    
    
    /**
     * constructor
     */
    public function  __construct() {
        $this->buildBasics();
        $this->buildInt();
        $this->buildDouble();
        $this->buildString();
        $this->buildXml();
        $this->buildDate();
        $this->buildArray();
        $this->buildObject();
        $this->buildByteArray();
        $this->buildVector();
        $this->buildDictionary();
    }

    /**
     * simple...
     */
    public function buildBasics(){
        $this->dUndefined = new Amfphp_Core_Amf_Types_Undefined();
        $this->sUndefined = pack('C', 0);
        $this->sNull = pack('C', 1);
        $this->sFalse = pack('C', 2);
        $this->sTrue = pack('C', 3);

    }

    /**
     * build int
     */
    public function buildInt(){
//pack('C', 4) : the data type first. Then the data...
// A negative integer is always serialised as U29-4.
// This method is not supposed to output the value type.

// For a '-1', all bits are set.
// All four bytes thus are '255'.

        $this->dInt1 = -1;
        $this->sInt1 = pack('C', 4) . pack('C4', 255, 255, 255, 255);
        

// An 8 bit non-negative integer is serialised as U29-2.
// The higher 7 bits are in the first byte, the remaining 7 bits in the second byte (0-based).
// 130 = 128(7th) + 2(1nd)

// bit 7-13 (0x01) plus continuation flag (0x80) = 0x80 + 0x01 = 0x81
// bit 0-6 without continuation flag = 0x02

        $this->dInt2 = 130;
        $this->sInt2 = pack('C', 4) . pack('CC', 129, 2);

    }

    /**
     * double: 0x05 as type marker, then 8 bytes. Careful of little/big endian so that test runs with both systems
     *
     */
    public function buildDouble(){
        $this->dDouble = 0.42;
        $packedData = pack('d', 0.42);
        $this->sDouble = pack('C', 5) . $this->revIfBigEndian($packedData);
    }

    /**
     * note: no type markers here, because the method doesn't do it. So only the raw data.
     * @todo methods with a type marker included
     *
     */
    public function buildString(){
        $this->dEmptyString = '';
        $this->sEmptyString = pack('C', '1');

        $this->dString = 'This is a test!';
// The string is seen for the first time. So no reference can be used.
// This method is not supposed to output the value type.

// 'U29S-value' (15 << 1 | 1 = 0x1f)
// 'This is a test!'
        $this->sString = pack('C', 0x1f) . 'This is a test!';

// The string is added twice. It first is added as value,
// the second time it should be added as reference.
// This method is not supposed to output the value type.

// 'U29S-value' (15 << 1 | 1 = 0x1f)
// 'This is a test!'
// 'U29S-ref' for lookup table index 0) (0 << 1 = 0x00)
        $this->sStringTwice = pack('C', 31) . 'This is a test!' . pack('C', 0);


    //@TODO mbstrings with that php overload. (ssee sourceforge bugs)
        
    }

    /**
     * build xml
     */
    public function buildXml(){
        $this->dXmlDocument = new Amfphp_Core_Amf_Types_XmlDocument('<?xml version=\'1.0\'?><testRoot><testChild1>testChild1Value</testChild1></testRoot>');
        //2nd is u29s-value : length << 1 | 1.
        $this->sXmlDocument = pack('C', 7) . pack('C2', 0x81, 0x25) . $this->dXmlDocument->data;

        //xml and xml doc treated the same. So do no tests for xml(not doc!) only for deserializer
        $this->dXml = new Amfphp_Core_Amf_Types_Xml('<?xml version=\'1.0\'?><testRoot><testChild1>testChild1Value</testChild1></testRoot>');
        $this->sXml = pack('C', 0x0B) . pack('C2', 0x81, 0x25) . $this->dXml->data;

    }

    /**
     * build date
     */
    public function buildDate(){
        $this->dDate = new Amfphp_Core_Amf_Types_Date(1306926779576); //1st June 2011
        //type: 0x08
        $this->sDate = pack('C', 0x08);
        //U29D-value = 1. Marker to distinguish from references, I think
        $this->sDate .= pack('C', 0x01);
        $dateData = pack('d', 1306926779576);
        //date is a double, see writeDouble for little/big endian
        $this->sDate .= $this->revIfBigEndian($dateData);
        
    }

    /**
     * build array
     */
    public function buildArray(){
        $this->dEmptyArray = array();
// 'array_marker' (0x09)
// 'U29A-value' with size of 'dense portion' (0 << 1 | 1 = 0x01)
// 'UTF-8-empty' flagging end of 'associative portion' (0x01)
        $this->sEmptyArray = pack('C3', 9, 1, 1);



// Serialise an array with 0-based integer keys (Spec calls it 'dense array').
        $this->dDenseArray = array(0 => 'zero', 1 => 'one');
// 'array_marker' (0x09)
// 'U29A-value' with size of 'dense portion' (2 << 1 | 1 = 0x05)
// 'UTF-8-empty' flagging end of 'associative portion' (0x01)
// 'string-marker' (0x06)
// 'U29S-value' with string length (4 << 1 | 1 = 0x09)
// 'zero'
// 'string-marker' (0x06)
// 'U29S-value' with string length (3 << 1 | 1 = 0x07)
// 'one'
        $this->sDenseArray = pack('C5', 9, 5, 1, 6, 9) . 'zero' . pack('CC', 6, 7) . 'one';


// Serialise an array with 0-based integer keys ('dense portion') plus
// non-integer keys ('associative portion').
        $this->dMixedArray =  array(0 => 'zero', 1 => 'one', 's' => 'sv', 'xyz' => 'tvxyz');

// 'array_marker' (0x09)
// 'U29A-value' with size of 'dense portion' (2 << 1 | 1 = 0x05)
// 1st 'assoc-value':
// - 'U29S-value' for string of length 1 (1 << 1 | 1 = 0x03)
// - 's'
// value:
// - 'string-marker' (0x06)
// - 'U29S-value' with string length (2 << 1 | 1 = 0x05)
// - 'sv'
// 2nd 'assoc-value':
// - 'U29S-value' for string of length 3 (3 << 1 | 1 = 0x07)
// - 'xyz'
// value:
// - 'string-marker' (0x06)
// - 'U29S-value' with string length (5 << 1 | 1 = 0x0b)
// - 'tvxyz'
// 'UTF-8-empty' flagging end of 'associative portion' (0x01)
// 'dense portion' values:
// - 'string-marker' (0x06)
// - 'U29S-value' with string length (4 << 1 | 1 = 0x09)
// - 'zero'
// 'dense portion' values:
// - 'string-marker' (0x06)
// - 'U29S-value' with string length (3 << 1 | 1 = 0x07)
// - 'one'
        $this->sMixedArray = pack('C3', 9, 5, 3) . 's' . pack('CC', 6, 5) . 'sv' . pack('C', 7) . 'xyz' . pack('CC', 6, 11) . 'tvxyz' . pack('C3', 1, 6, 9) . 'zero' . pack('CC', 6, 7) . 'one';
    }

    /**
     * build object
     */
    public function buildObject(){
        $this->dObject = $this->createDummyObj();
        //object marker
        $this->sObject = pack('C', 0x0A);
        //traits.
        $this->sObject .= pack('C', 0x13);
        //class name length, on a U29-1 : 11 << 1 | 1 = 23 
        $this->sObject .= pack('C', 0x17);
        //class name
        $this->sObject .= 'DummyClass2';
        //member name length, on a U29-1 : 4 << 1 | 1 = 9
        $this->sObject .= pack('C', 0x9);
        //member name
        $this->sObject .= 'data';
        //string marker
        $this->sObject .= pack('C', 6);
        //member value length, on a U29-1 : 4 << 1 | 1 = 9
        $this->sObject .= pack('C', 0x9);
        //member value
        $this->sObject .= 'test';
        
    }

    /**
     * build byte array
     */
    public function buildByteArray(){
        $this->dByteArray = new Amfphp_Core_Amf_Types_ByteArray('test');
        //Byte Array Marker
        $this->sByteArray = pack('C', 0x0C);
        //only the data field of ByteArray is encoded, the class is just a wrapper!
        //data length, on a U29-1 : 4 << 1 | 1 = 9
        $this->sByteArray .= pack('C', 9);
        //data bytes
        $this->sByteArray .= 'test';

        
    }
    
    /**
     * revers packed data if big endian 
     * @see Amfphp_Core_Amf_Util
     * @param string $packedData
     * @return string 
     */
    private function revIfBigEndian($packedData){
        if(Amfphp_Core_Amf_Util::isSystemBigEndian()){
            return strrev($packedData);
        }else{
            return $packedData;
        }
        
    }
    

    /**
     * build vector (different types)
     */
    public function buildVector(){
        //int 
        $this->dVectorInt = new Amfphp_Core_Amf_Types_Vector();
        $this->dVectorInt->type = Amfphp_Core_Amf_Types_Vector::VECTOR_INT;
        $this->dVectorInt->data = array(1,2,3);
        //int vector type marker
        $this->sVectorInt = pack('C', 0x0D);
        //data length, on a U29-1 : 3 << 1 | 1 = 7
        $this->sVectorInt .= $this->revIfBigEndian(pack('C', 7));
        //not fixed
        $this->sVectorInt .= pack('C', 0);
        //data (U32)
        $this->sVectorInt .= $this->revIfBigEndian(pack('i', 1));
        $this->sVectorInt .= $this->revIfBigEndian(pack('i', 2));
        $this->sVectorInt .= $this->revIfBigEndian(pack('i', 3));

        
        //uint
        $this->dVectorUint = new Amfphp_Core_Amf_Types_Vector();
        $this->dVectorUint->type = Amfphp_Core_Amf_Types_Vector::VECTOR_UINT;
        $this->dVectorUint->data = array(1,2,3);
        //Uint vector type marker
        $this->sVectorUint = pack('C', 0x0E);
        //data length, on a U29-1 : 3 << 1 | 1 = 7
        $this->sVectorUint .= $this->revIfBigEndian(pack('C', 7));
        //not fixed
        $this->sVectorUint .= pack('C', 0);
        //data (U32)
        $this->sVectorUint .= $this->revIfBigEndian(pack('I', 1));
        $this->sVectorUint .= $this->revIfBigEndian(pack('I', 2));
        $this->sVectorUint .= $this->revIfBigEndian(pack('I', 3));

        
        //double
        $this->dVectorDouble = new Amfphp_Core_Amf_Types_Vector();
        $this->dVectorDouble->type = Amfphp_Core_Amf_Types_Vector::VECTOR_DOUBLE;
        $this->dVectorDouble->data = array(0.1, 0.2, 0.3);
        //Double vector type marker
        $this->sVectorDouble = pack('C', 0x0F);
        //data length, on a U29-1 : 3 << 1 | 1 = 7
        $this->sVectorDouble .= $this->revIfBigEndian(pack('C', 7));
        //not fixed
        $this->sVectorDouble .= pack('C', 0);
        //data (U32)
        $this->sVectorDouble .= $this->revIfBigEndian(pack('d', 0.1));
        $this->sVectorDouble .= $this->revIfBigEndian(pack('d', 0.2));
        $this->sVectorDouble .= $this->revIfBigEndian(pack('d', 0.3));

        
        //object
        $this->dVectorObject = new Amfphp_Core_Amf_Types_Vector();
        $this->dVectorObject->type = Amfphp_Core_Amf_Types_Vector::VECTOR_OBJECT;
        $this->dVectorObject->data = array($this->createDummyObj());
        $this->dVectorObject->className = 'DummyClass2';
        //Object vector type marker
        $this->sVectorObject = pack('C', 0x010);
        //data length, on a U29-1 : 1 << 1 | 1 = 3
        $this->sVectorObject .= $this->revIfBigEndian(pack('C', 3));
        //not fixed
        $this->sVectorObject .= pack('C', 0);
        //class name length, on a U29-1 : 11 << 1 | 1 = 23 
        $this->sVectorObject .= pack('C', 0x17);
        //class name
        $this->sVectorObject .= 'DummyClass2';
        //data (dummy object)
        //object marker
        $this->sVectorObject .= pack('C', 0x0A);
        //traits.
        $this->sVectorObject .= pack('C', 0x13);
        //null
        $this->sVectorObject .= pack('C', 0x0);
        //member name length, on a U29-1 : 4 << 1 | 1 = 9
        $this->sVectorObject .= pack('C', 0x9);
        //member name
        $this->sVectorObject .= 'data';
        //string marker
        $this->sVectorObject .= pack('C', 6);
        //member value length, on a U29-1 : 4 << 1 | 1 = 9
        $this->sVectorObject .= pack('C', 0x9);
        //member value
        $this->sVectorObject .= 'test';

        
    }    
    
    private function createDummyObj(){
        $ret = new stdClass();
        $ret->data= 'test';
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $ret->$explicitTypeField ='DummyClass2'; 
        return $ret;
    }

    /**
     * build dictionary
     * @todo
     */
    public function buildDictionary(){


        
    }     
}


?>
