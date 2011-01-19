<?php
    /**
 * test data for the AMFPHP unit tests
 * data types have the s prefix for "serialized" and "d" prefix for "deserialized"
 * for Packets there is a flaw in the AMFphp design which means that serializng and deserializing is not symmetrical.
 * so use s for serialized, d for deserialized for the serialization tests and dd for the deserialation tests, the idea being that dd will disappear for v2
 *
 * @author Ariel Sommeria-klein
 */
class AMFTestData {
    //fundamental (stream) types
    public $sByte;
    public $dByte;
    public $sInt;
    public $dInt;
    public $sLong;
    public $dLong;
    public $sDouble;
    public $dDouble;
    public $sUtf;
    public $dUtf;
    public $sLongUtf;
    public $dLongUtf;
    public $sBinary;
    public $dBinary;

    //AMF data types
    public $sNumber;
    public $dNumber;
    public $sBoolean;
    public $dBoolean;
    public $sString;
    public $dString;
    public $sObject;
    public $dObject;
    public $sNull;
    public $dNull;
    public $sUndefined;
    public $dUndefined;
    public $sReference;
    public $dReference;
    public $sEcmaArray;
    public $dEcmaArray;
    public $sObjectEnd;
    public $dObjectEnd;
    public $sStrictArray;
    public $dStrictArray;
    public $sDate;
    public $dDate;
    public $sLongString;
    public $dLongString;
    public $sUnsupported;
    public $dUnsupported;
    public $sXml;
    public $dXml;
    public $sTypedObject;
    public $dTypedObject;
    public $dTypedObjectAsArray;

    //AMF Packet objects
    public $sEmptyPacket;
    public $dEmptyPacket;
    public $sNullHeaderPacket;
    public $dNullHeaderPacket;
    public $sStringHeaderPacket;
    public $dStringHeaderPacket;
    public $sNullMessagePacket;
    public $dNullMessagePacket;
    public $sStringMessagePacket;
    public $dStringMessagePacket;
    public $s2Headers2MessagesPacket;
    public $d2Headers2MessagesPacket;

    public $mirrorServiceRequestPacket;
    public $mirrorServiceResponsePacket;
    
    public function  __construct() {
        $this->buildByte();
        $this->buildInt();
        $this->buildLong();
        $this->buildDouble();
        $this->buildUtf();
        $this->buildLongUtf();
        $this->buildNumber();
        $this->buildBoolean();
        $this->buildString();
        $this->buildObject();
        $this->buildNull();
        $this->buildUndefined();
        $this->buildReference();
        $this->buildEcmaArray();
        $this->buildObjectEnd();
        $this->buildStrictArray();
        $this->buildDate();
        $this->buildLongString();
        $this->buildUnsupported();
        $this->buildXml();
        $this->buildTypedObject();
        $this->buildEmptyPacket();
        $this->buildNullHeaderPacket();
        $this->buildStringHeaderPacket();
        $this->buildNullMessagePacket();
        $this->buildStringMessagePacket();
        $this->build2HeadersAndTwoMessagesPacket();
        $this->buildSimpleMirrorServiceRequestAndResponse();
        //$this->build;

    }


    /**
     * byte
     */
    public function buildByte(){
        $this->dByte = 42;
        $this->sByte = pack('C', 42);
    }

    /**
     * int: 2 bytes
     *
     */
    public function buildInt(){
        $this->dInt = 42;
        $this->sInt = pack('n', 42);
    }

    /**
     * long: 4 bytes
     */
    public function buildLong(){
        $this->dLong = 42;
        $this->sLong = pack('N', 42);
    }

    /**
     * double: 8 bytes. Careful of little/big endian so that test runs with both systems
     *
     */
    public function buildDouble(){
        $this->dDouble = 42;
        $this->sDouble = pack("d", 42);
        if(AMFUtil::isSystemBigEndian()){
            $this->sDouble = strrev($this->sDouble);
        }
    }


    /**
     * utf: the length of the data on 2 bytes and then the char data
     */
    public function buildUtf(){
        $testString = "test string";
        $this->dUtf = $testString;
        $this->sUtf = pack('n', strLen($testString));
        $this->sUtf .= $testString;
    }


    /**
     * long utf: the length of the data on 4 bytes and then the char data. The char data is more than 65xxx long
     */
    public function buildLongUtf(){
        $testString = str_repeat("a", 70000);
        $this->dLongUtf = $testString;
        $this->sLongUtf = pack('N', strLen($testString));
        $this->sLongUtf .= $testString;
    }


     /**
     * actual data types from the spec.
     */

     /**
      * number: type is0, then value in (double)8 bytes. See buildDouble for little/big endian
      */
    public function buildNumber(){
        $this->dNumber = 42;
        //type: 0
        $this->sNumber = pack('C', 0);
        //number
        $numberData = pack("d", 42);
        if(AMFUtil::isSystemBigEndian()){
            $numberData = strrev($numberData);
        }

        $this->sNumber .= $numberData;
    }


    public function buildBoolean(){
        $this->dBoolean = FALSE;
        //type: 1
        $this->sBoolean = pack('C', 1);
        //data : FALSE
        $this->sBoolean .= pack('C', FALSE);
    }


    public function buildString(){
        $this->dString = "test string";
        //type : 2
        $this->sString = pack('C', 2);
        //data length on an int
        $this->sString .= pack('n', strlen("test string"));
        //data
        $this->sString .= "test string";
    }


    public function buildObject(){
        $this->dObject = Array("firstKey" => "firstValue", "secondKey" =>"secondValue");
        //type : 3
        $this->sObject = pack('C', 3);

        /**
         * first entry in object
         */

        //key length on an int
        $this->sObject .= pack('n', strLen("firstKey"));
        //data
        $this->sObject .= "firstKey";
        //data type is string, so use string(2)
        $this->sObject .= pack('C', 2);
        //data length
        $this->sObject .= pack('n', strLen("firstValue"));
        //data
        $this->sObject .= "firstValue";

        /**
         * second entry in object
         */

        //key length on an int
        $this->sObject .= pack('n', strLen("secondKey"));
        //data
        $this->sObject .= "secondKey";
        //data type is string, so use string(2)
        $this->sObject .= pack('C', 2);
        //data length
        $this->sObject .= pack('n', strLen("secondValue"));
        //data
        $this->sObject .= "secondValue";

        //object end
        $this->sObject .= pack("Cn", 0, 9);
    }


    public function buildNull(){
        $this->dNull = NULL;
        //type: 5
        $this->sNull = pack('C', 5);
    }

    public function buildUndefined(){
        $this->dUndefined = new Undefined();
        //type: 6
        $this->sUndefined = pack('C', 6);

    }

    /**
     * TODO test with a real reference?
     */
    public function buildReference(){
        $this->dReference = 12345;
        //type: 7
        $this->sReference = pack('C', 7);
        //reference on an int.
        $this->sReference .= pack('n', 12345);
    }

    /**
     * the writeArray method looks at the keys. If there are both numeric and string keys, the data is treated as an Ecma Array
     * it also sorts the data and writes the data with numerical keys first, then the data with string keys
     */
    public function buildEcmaArray(){
        $this->dEcmaArray = Array("firstKey" => "firstValue", 0 =>"secondValue");
        //type : 8
        $this->sEcmaArray = pack('C', 8);
        //number of sub objects on a long
        //TODO the spec says count of all sub objects(here 2) , whereas the code says count of objects with numerical keys(here 1). Clean? A.S.
        $this->sEcmaArray .= pack('N', 1);


        /**
         * first entry in object. (0->secondValue, because of sorting)
         */
        //key length on an int
        $this->sEcmaArray .= pack('n', strLen("0"));
        //data
        $this->sEcmaArray .= "0";
        //data type is string, so use string(2)
        $this->sEcmaArray .= pack('C', 2);
        //data length
        $this->sEcmaArray .= pack('n', strLen("secondValue"));
        //data
        $this->sEcmaArray .= "secondValue";

        /**
         * second entry in object
         */

        //key length on an int
        $this->sEcmaArray .= pack('n', strLen("firstKey"));
        //data
        $this->sEcmaArray .= "firstKey";
        //data type is string, so use string(2)
        $this->sEcmaArray .= pack('C', 2);
        //data length
        $this->sEcmaArray .= pack('n', strLen("firstValue"));
        //data
        $this->sEcmaArray .= "firstValue";


        $this->sEcmaArray .= pack("Cn", 0, 9);

    }
    
    public function buildObjectEnd(){
        $this->dObjectEnd = NULL;
        //type: 9
        $this->sObjectEnd = pack('ccc', 0, 0, 9);

    }


    public function buildStrictArray(){
        $this->dStrictArray = Array("firstValue", "secondValue");
        //type : 0x0A
        $this->sStrictArray = pack('C', 0x0A);
        //number of sub objects on a long
        $this->sStrictArray .= pack('N', 2);


        /**
         * first entry in object. (0->secondValue, because of sorting)
         */
        //data type is string, so use string(2)
        $this->sStrictArray .= pack('C', 2);
        //data length
        $this->sStrictArray .= pack('n', strLen("firstValue"));
        //data
        $this->sStrictArray .= "firstValue";

        /**
         * second entry in object
         */

        //data type is string, so use string(2)
        $this->sStrictArray .= pack('C', 2);
        //data length
        $this->sStrictArray .= pack('n', strLen("secondValue"));
        //data
        $this->sStrictArray .= "secondValue";


        /**
         * note : no end object marker!
         */

    }

    public function buildDate(){
        $this->dDate = 1234;
        //type: 0x0B
        $this->sDate = pack('C', 0x0B);
        //date is a double, see writeDouble for little/big endian
        $dateData = pack("d", 1234);
        if(AMFUtil::isSystemBigEndian()){
            $dateData = strrev($dateData);
        }
        $this->sDate .= $dateData;
        //time zone, not supported. int set to 0
        $this->sDate .= pack('n', 0);


    }

    public function buildLongString(){
        //needs to be more than 65535 characters.
        $this->dLongString = str_repeat("a", 70000);
        //type : 0x0C
        $this->sLongString = pack('C', 0x0C);
        //data length on a long
        $this->sLongString .= pack('N', strlen($this->dLongString));
        //data
        $this->sLongString .= $this->dLongString;
    }


    /**
     * TODO: no writeUnsupported method, and no PHP for unsupported. Write it A.S.
     */
    public function buildUnsupported(){
        $this->dUnsupported = "unsupported";
        //type: 0x0D
        $this->sUnsupported = pack('C', 0x0D);
    }

    /**
     * note: the writeXml method gets rids of CRs and LFs
     */
    public function buildXml(){
        $this->dXml = "<testXml>test</testXml>";
        //type : 0xOF
        $this->sXml = pack('C', 0x0F);
        //data length on a long
        $this->sXml .= pack('N', strlen("<testXml>test</testXml>"));
        //data
        $this->sXml .= "<testXml>test</testXml>";
    }

    /**
     * note: the writeXml method gets rids of CRs and LFs
     */
    public function buildTypedObject(){
        $this->dTypedObject = new stdClass();
        $this->dTypedObject->data = "dummyData";
        $this->dTypedObject->_explicitType = "DummyClass";
        //type : 0x10
        $this->sTypedObject = pack('C', 0x10);
        //class name length on a int
        $this->sTypedObject .= pack('n', strLen("DummyClass"));
        //class name
        $this->sTypedObject .= "DummyClass";
        //length of member obj name on an int
        $this->sTypedObject .= pack('n', strLen("data"));
        //member obj
        $this->sTypedObject .= "data";
        //type of member obj: string (0x02)
        $this->sTypedObject .= pack('C', 0x02);
        //length of member obj value on an int
        $this->sTypedObject .= pack('n', strLen("dummyData"));
        //member obj value
        $this->sTypedObject .= "dummyData";
        // end object marker
        $this->sTypedObject .= pack("Cn", 0, 9);
    }



   /**
    * AMF Packets
    */

    /**
     * test serializing an empty AMFPacket.
     * expected output: 0x000000
     * 1st int : version
     * 2nd int : number of headers
     * 3rd int : number of Messages
     */
    public function buildEmptyPacket(){
        $this->dEmptyPacket = new AMFPacket();
        $this->sEmptyPacket = pack('nnn', 0, 0, 0);
    }

    /**
     * one header containing a null, and with required set to true
     */
    public function buildNullHeaderPacket(){
        $nullHeader = new AMFHeader("null header", TRUE, NULL);

        $this->dNullHeaderPacket = new AMFPacket();
	array_push($this->dNullHeaderPacket->headers, $nullHeader);

        //version (int)
        $this->sNullHeaderPacket = pack('n', 0);
        //number of headers (int)
        $this->sNullHeaderPacket .= pack('n', 1);
        //header name length (int)
        $this->sNullHeaderPacket .= pack('n', strlen($nullHeader->name));
        //header name
        $this->sNullHeaderPacket .= $nullHeader->name;
        //required (here true, cf constructor of $nullHeader)
        $this->sNullHeaderPacket .= pack('C', 1);

        //null type indicator (byte)
        $headerValueData = pack('C', 5);

        //header value length (long)
        $this->sNullHeaderPacket .= pack('N', strlen($headerValueData));
        //header value
        $this->sNullHeaderPacket .= $headerValueData;

        //number of Messages
        $this->sNullHeaderPacket .= pack('n', 0);

    }

    /**
     *  with one header containing a string
     */
    public function buildStringHeaderPacket(){
        $stringHeader = new AMFHeader("string header", FALSE, "zzzzzz");

        $this->dStringHeaderPacket = new AMFPacket();
	array_push($this->dStringHeaderPacket->headers, $stringHeader);
        //version (int)
        $this->sStringHeaderPacket = pack('n', 0);
        //number of headers (int)
        $this->sStringHeaderPacket .= pack('n', 1);
        //header name length (int)
        $this->sStringHeaderPacket .= pack('n', strlen($stringHeader->name));
        //header name
        $this->sStringHeaderPacket .= $stringHeader->name;
        //required(false)
        $this->sStringHeaderPacket .= pack('C', 0);

        //string type indicator (byte)
        $headerValueData = pack('C', 2);
        //header value length (int)
        $headerValueData .= pack('n', strlen($stringHeader->value));
        //header value (works because the value is a string)
        $headerValueData .= $stringHeader->value;

        //header value length (long)
        $this->sStringHeaderPacket .= pack('N', strlen($headerValueData));
        //header value
        $this->sStringHeaderPacket .= $headerValueData;

        //number of Messages
        $this->sStringHeaderPacket .= pack('n', 0);

    }


    /**
     * no headers and a Message containing a null
     */
    public function buildNullMessagePacket(){
        $nullMessage = new AMFMessage();
        $nullMessage->targetURI = "/onStatus";
        $nullMessage->responseURI = "null";
        $this->dNullMessagePacket = new AMFPacket();
        array_push($this->dNullMessagePacket->messages, $nullMessage);

        //version (int)
        $this->sNullMessagePacket = pack('n', 0);
        //number of headers (int)
        $this->sNullMessagePacket .= pack('n', 0);
        //number of Messages
        $this->sNullMessagePacket .= pack('n', 1);
        //target uri length
        $this->sNullMessagePacket .= pack('n', 9);
        //target uri.
        $this->sNullMessagePacket .= "/onStatus";
        //response uri length
        $this->sNullMessagePacket .= pack('n', 4);
        //response uri.
        $this->sNullMessagePacket .= "null";

        //result is NULL by default. this is one byte for type that is worth 5, and no data
        $messageResultsData = pack('C', 5);
        //result length, long
        $this->sNullMessagePacket .= pack('N', strlen($messageResultsData));
        //add the result itself
        $this->sNullMessagePacket .= $messageResultsData;

    }

    /**
     *  no headers and a Message containing a string
     */
    public function buildStringMessagePacket(){
        $stringMessage = new AMFMessage();
        $testString = "test string";
        $stringMessage->targetURI = "/onStatus";
        $stringMessage->responseURI = "null";
        $stringMessage->data = $testString;
        $this->dStringMessagePacket = new AMFPacket();
        array_push($this->dStringMessagePacket->messages, $stringMessage);

        //version (int)
        $this->sStringMessagePacket = pack('n', 0);
        //number of headers (int)
        $this->sStringMessagePacket .= pack('n', 0);
        //number of Messages
        $this->sStringMessagePacket .= pack('n', 1);
        //target uri length
        $this->sStringMessagePacket .= pack('n', 9);
        //target uri.
        $this->sStringMessagePacket .= "/onStatus";
        //response uri length
        $this->sStringMessagePacket .= pack('n', 4);
        //response uri. default is "null"
        $this->sStringMessagePacket .= "null";

        //result is string. byte with '2' as data type, then length, then char data
        $messageResultsData = pack('C', 2) . pack('n', strLen($testString)) . $testString;
        //result length, long
        $this->sStringMessagePacket .= pack('N', strlen($messageResultsData));
        //add the result itself
        $this->sStringMessagePacket .= $messageResultsData;

    }


    /**
     * an AMFPacket with two headers one with a string and one with a null , and two Messages, one with a string and one with a null
     */
    public function build2HeadersAndTwoMessagesPacket(){
        $nullHeader = new AMFHeader("null header", TRUE, NULL);
        $stringHeader = new AMFHeader("string header", FALSE, "zzzzzz");
        $nullMessage = new AMFMessage();
        $nullMessage->targetURI = "/onStatus";
        $nullMessage->responseURI = "null";
        $stringMessage = new AMFMessage();
        $testString = "test string";
        $stringMessage->targetURI = "/onStatus";
        $stringMessage->responseURI = "null";
        $stringMessage->data = $testString;

        $this->d2Headers2MessagesPacket = new AMFPacket();
	array_push($this->d2Headers2MessagesPacket->headers, $stringHeader);
	array_push($this->d2Headers2MessagesPacket->headers, $nullHeader);
        array_push($this->d2Headers2MessagesPacket->messages, $stringMessage);
        array_push($this->d2Headers2MessagesPacket->messages, $nullMessage);
        //version (int)
        $this->s2Headers2MessagesPacket = pack('n', 0);
        //number of headers (int)
        $this->s2Headers2MessagesPacket .= pack('n', 2);

        /**
         * first header (string)
         */
        //header name length (int)
        $this->s2Headers2MessagesPacket .= pack('n', strlen($stringHeader->name));
        //header name
        $this->s2Headers2MessagesPacket .= $stringHeader->name;
        //required(false) (byte)
        $this->s2Headers2MessagesPacket .= pack('C', 0);

        //string type indicator (byte)
        $headerValueData = pack('C', 2);
        //header value length (int)
        $headerValueData .= pack('n', strlen($stringHeader->value));
        //header value (works because the value is a string)
        $headerValueData .= $stringHeader->value;

        //header value length (long)
        $this->s2Headers2MessagesPacket .= pack('N', strlen($headerValueData));
        //header value
        $this->s2Headers2MessagesPacket .= $headerValueData;

        /**
         * second header (null)
         */
        //header name length (int)
        $this->s2Headers2MessagesPacket .= pack('n', strlen($nullHeader->name));
        //header name
        $this->s2Headers2MessagesPacket .= $nullHeader->name;
        //required (here true, cf constructor of $nullHeader)
        $this->s2Headers2MessagesPacket .= pack('C', 1);

        //string type indicator (byte)
        $headerValueData = pack('C', 5);
        //header value length (long)
        $this->s2Headers2MessagesPacket .= pack('N', strlen($headerValueData));
        //header value
        $this->s2Headers2MessagesPacket .= $headerValueData;

        /**
         * Messages
         */
        //number of Messages
        $this->s2Headers2MessagesPacket .= pack('n', 2);

        /**
         * first Message (string)
         */
        //target uri length
        $this->s2Headers2MessagesPacket .= pack('n', 9);
        //target uri. This is responseIndex (default is "") + "/onStatus"
        $this->s2Headers2MessagesPacket .= "/onStatus";
        //response uri length
        $this->s2Headers2MessagesPacket .= pack('n', 4);
        //response uri. default is "null"
        $this->s2Headers2MessagesPacket .= "null";

        //result is string. byte with '2' as data type, then length, then char data
        $messageResultsData = pack('C', 2) . pack('n', strLen($testString)) . $testString;
        //result length, long
        $this->s2Headers2MessagesPacket .= pack('N', strlen($messageResultsData));
        //add the result itself
        $this->s2Headers2MessagesPacket .= $messageResultsData;

        /**
         * second Message (null)
         */
        //target uri length
        $this->s2Headers2MessagesPacket .= pack('n', 9);
        //target uri. This is responseIndex (default is "") + "/onStatus"
        $this->s2Headers2MessagesPacket .= "/onStatus";
        //response uri length
        $this->s2Headers2MessagesPacket .= pack('n', 4);
        //response uri. default is "null"
        $this->s2Headers2MessagesPacket .= "null";

        //result is null
        $messageResultsData = pack('C', 5);
        //result length, long
        $this->s2Headers2MessagesPacket .= pack('N', strlen($messageResultsData));
        //add the result itself
        $this->s2Headers2MessagesPacket .= $messageResultsData;

    }

    /**
     * Packets with a proper response, used to test gateway. dependant on service used. Here all will be based on mirror test, where the response data is
     * the same as the request data.
     */


    /**
     *
     */
    public function buildSimpleMirrorServiceRequestAndResponse(){

        //request

        $requestTargetURI = "MirrorService/returnOneParam";
        $requestResponseURI = "/1";

        //version (int)
        $requestPacket = pack('n', 0);
        //number of headers (int)
        $requestPacket .= pack('n', 0);
        //number of Messages
        $requestPacket .= pack('n', 1);
        //target uri length
        $requestPacket .= pack('n', strlen($requestTargetURI));
        //target uri .
        $requestPacket .= $requestTargetURI;
        //response uri length
        $requestPacket .= pack('n', strlen($requestResponseURI));
        //response uri.
        $requestPacket .= $requestResponseURI;

        //the function call parameters, and the returned data are the same with the mirror service.
        ////here a strict array containing a string
        //type : 0x0A
        $requestMessage = pack('C', 0x0A);
        //number of sub objects on a long
        $requestMessage .= pack('N', 1);

        //the contained string
        //data type is string, so use string(2)
        $requestMessage .= pack('C', 2);
        //data length
        $requestMessage .= pack('n', strLen("testString"));
        //data
        $requestMessage .= "testString";
        $requestMessageLength = strLen($requestMessage);

        //Message length, long
        $requestPacket .= pack('N', $requestMessageLength);
        //add the Message itself
        $requestPacket .= $requestMessage;

        $this->mirrorServiceRequestPacket = $requestPacket;





        //response

        $responseTargetURI = "/1/onResult";
        $responseResponseURI = "null";

        //version (int)
        $responsePacket = pack('n', 0);
        //number of headers (int)
        $responsePacket .= pack('n', 0);
        //number of Messages
        $responsePacket .= pack('n', 1);
        //target uri length
        $responsePacket .= pack('n', strlen($responseTargetURI));
        //target uri .
        $responsePacket .= $responseTargetURI;
        //response uri length
        $responsePacket .= pack('n', strlen($responseResponseURI));
        //response uri.
        $responsePacket .= $responseResponseURI;

        //response Message. here the string sent in the request
        //data type is string, so use string(2)
        $responseMessage = pack('C', 2);
        //data length
        $responseMessage .= pack('n', strLen("testString"));
        //data
        $responseMessage .= "testString";
        $responseMessageLength = strLen($responseMessage);

        //Message length, long
        $responsePacket .= pack('N', $responseMessageLength);
        //add the Message itself
        $responsePacket .= $responseMessage;


        $this->mirrorServiceResponsePacket = $responsePacket;

    }



}


/**
 * used for testing with typed objects
 */
class DummyClass{
    public $data;
}
?>
