<?php
    /**
 * test data for the AMFPHP unit tests
 * data types have the s prefix for "serialized" and "d" prefix for "deserialized"
 * for messages there is a flaw in the AMFphp design which means that serializng and deserializing is not symmetrical.
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

    //AMF message objects
    public $sEmptyMessage;
    public $dEmptyMessage;
    public $sNullHeaderMessage;
    public $dNullHeaderMessage;
    public $ddNullHeaderMessage;
    public $sStringHeaderMessage;
    public $dStringHeaderMessage;
    public $ddStringHeaderMessage;
    public $sNullBodyMessage;
    public $dNullBodyMessage;
    public $ddNullBodyMessage;
    public $sStringBodyMessage;
    public $dStringBodyMessage;
    public $ddStringBodyMessage;
    public $s2Headers2BodiesMessage;
    public $d2Headers2BodiesMessage;
    public $dd2Headers2BodiesMessage;

    public $mirrorServiceRequestMessage;
    public $mirrorServiceResponseMessage;
    
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
        $this->buildEmptyMessage();
        $this->buildNullHeaderMessage();
        $this->buildStringHeaderMessage();
        $this->buildNullBodyMessage();
        $this->buildStringBodyMessage();
        $this->build2HeadersAndTwoBodiesMessage();
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

    /**
     * TODO: no writeUndefined method. Write or understand why A.S. define a PHP for "undefined"
     */
    public function buildUndefined(){
        $this->dUndefined = "undefined";
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

    /**
     * TODO: no writeObjectEnd method. Write it, and get the number of bytes right (0X09, not 0X009) A.S.
     */
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
        $this->dTypedObjectAsArray = array("data" => "dummyData", "_explicitType" => "DummyClass");
        $this->dTypedObject = new DummyClass();
        $this->dTypedObject->data = "dummyData";
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
    * AMF messages
    */

    /**
     * test serializing an empty AMFMessage.
     * expected output: 0x000000
     * 1st int : version
     * 2nd int : number of headers
     * 3rd int : number of bodies
     */
    public function buildEmptyMessage(){
        $this->dEmptyMessage = new AMFMessage();
        $this->sEmptyMessage = pack('nnn', 0, 0, 0);
    }

    /**
     * one header containing a null, and with required set to true
     */
    public function buildNullHeaderMessage(){
        $nullHeader = new AMFHeader("null header", TRUE, NULL);

        $this->dNullHeaderMessage = new AMFMessage();
        $this->dNullHeaderMessage->addHeader($nullHeader);
        $this->ddNullHeaderMessage = new AMFMessage();
        $this->ddNullHeaderMessage->addHeader($nullHeader);

        //version (int)
        $this->sNullHeaderMessage = pack('n', 0);
        //number of headers (int)
        $this->sNullHeaderMessage .= pack('n', 1);
        //header name length (int)
        $this->sNullHeaderMessage .= pack('n', strlen($nullHeader->name));
        //header name
        $this->sNullHeaderMessage .= $nullHeader->name;
        //required (here true, cf constructor of $nullHeader)
        $this->sNullHeaderMessage .= pack('C', 1);

        //null type indicator (byte)
        $headerValueData = pack('C', 5);

        //header value length (long)
        $this->sNullHeaderMessage .= pack('N', strlen($headerValueData));
        //header value
        $this->sNullHeaderMessage .= $headerValueData;

        //number of bodies
        $this->sNullHeaderMessage .= pack('n', 0);

    }

    /**
     *  with one header containing a string
     */
    public function buildStringHeaderMessage(){
        $stringHeader = new AMFHeader("string header", FALSE, "zzzzzz");

        $this->dStringHeaderMessage = new AMFMessage();
        $this->dStringHeaderMessage->addHeader($stringHeader);
        $this->ddStringHeaderMessage = new AMFMessage();
        $this->ddStringHeaderMessage->addHeader($stringHeader);
        //version (int)
        $this->sStringHeaderMessage = pack('n', 0);
        //number of headers (int)
        $this->sStringHeaderMessage .= pack('n', 1);
        //header name length (int)
        $this->sStringHeaderMessage .= pack('n', strlen($stringHeader->name));
        //header name
        $this->sStringHeaderMessage .= $stringHeader->name;
        //required(false)
        $this->sStringHeaderMessage .= pack('C', 0);

        //string type indicator (byte)
        $headerValueData = pack('C', 2);
        //header value length (int)
        $headerValueData .= pack('n', strlen($stringHeader->value));
        //header value (works because the value is a string)
        $headerValueData .= $stringHeader->value;

        //header value length (long)
        $this->sStringHeaderMessage .= pack('N', strlen($headerValueData));
        //header value
        $this->sStringHeaderMessage .= $headerValueData;

        //number of bodies
        $this->sStringHeaderMessage .= pack('n', 0);

    }


    /**
     * no headers and a body containing a null
     */
    public function buildNullBodyMessage(){
        $nullBody = new AMFBody();
        $nullBody->targetURI = "null";
        $nullBody->responseURI = "/onStatus";
        $this->dNullBodyMessage = new AMFMessage();
        $this->dNullBodyMessage->addBody($nullBody);

        //version (int)
        $this->sNullBodyMessage = pack('n', 0);
        //number of headers (int)
        $this->sNullBodyMessage .= pack('n', 0);
        //number of bodies
        $this->sNullBodyMessage .= pack('n', 1);
        //response uri length
        $this->sNullBodyMessage .= pack('n', 9);
        //response uri. This is responseIndex (default is "") + "/onStatus"
        $this->sNullBodyMessage .= "/onStatus";
        //response target length
        $this->sNullBodyMessage .= pack('n', 4);
        //response target. default is "null"
        $this->sNullBodyMessage .= "null";

        //result is NULL by default. this is one byte for type that is worth 5, and no data
        $bodyResultsData = pack('C', 5);
        //result length, long
        $this->sNullBodyMessage .= pack('N', strlen($bodyResultsData));
        //add the result itself
        $this->sNullBodyMessage .= $bodyResultsData;

    }

    /**
     *  no headers and a body containing a string
     */
    public function buildStringBodyMessage(){
        $stringBody = new AMFBody();
        $testString = "test string";
        $stringBody->targetURI = "null";
        $stringBody->responseURI = "/onStatus";
        $stringBody->data = $testString;
        $this->dStringBodyMessage = new AMFMessage();
        $this->dStringBodyMessage->addBody($stringBody);

        //version (int)
        $this->sStringBodyMessage = pack('n', 0);
        //number of headers (int)
        $this->sStringBodyMessage .= pack('n', 0);
        //number of bodies
        $this->sStringBodyMessage .= pack('n', 1);
        //response uri length
        $this->sStringBodyMessage .= pack('n', 9);
        //response uri. This is responseIndex (default is "") + "/onStatus"
        $this->sStringBodyMessage .= "/onStatus";
        //response target length
        $this->sStringBodyMessage .= pack('n', 4);
        //response target. default is "null"
        $this->sStringBodyMessage .= "null";

        //result is string. byte with '2' as data type, then length, then char data
        $bodyResultsData = pack('C', 2) . pack('n', strLen($testString)) . $testString;
        //result length, long
        $this->sStringBodyMessage .= pack('N', strlen($bodyResultsData));
        //add the result itself
        $this->sStringBodyMessage .= $bodyResultsData;

    }


    /**
     * an AMFMessage with two headers one with a string and one with a null , and two bodies, one with a string and one with a null
     */
    public function build2HeadersAndTwoBodiesMessage(){
        $nullHeader = new AMFHeader("null header", TRUE, NULL);
        $stringHeader = new AMFHeader("string header", FALSE, "zzzzzz");
        $nullBody = new AMFBody();
        $nullBody->targetURI = "null";
        $nullBody->responseURI = "/onStatus";
        $stringBody = new AMFBody();
        $testString = "test string";
        $stringBody->targetURI = "null";
        $stringBody->responseURI = "/onStatus";
        $stringBody->data = $testString;

        $this->d2Headers2BodiesMessage = new AMFMessage();
        $this->d2Headers2BodiesMessage->addHeader($stringHeader);
        $this->d2Headers2BodiesMessage->addHeader($nullHeader);
        $this->d2Headers2BodiesMessage->addBody($stringBody);
        $this->d2Headers2BodiesMessage->addBody($nullBody);
        //version (int)
        $this->s2Headers2BodiesMessage = pack('n', 0);
        //number of headers (int)
        $this->s2Headers2BodiesMessage .= pack('n', 2);

        /**
         * first header (string)
         */
        //header name length (int)
        $this->s2Headers2BodiesMessage .= pack('n', strlen($stringHeader->name));
        //header name
        $this->s2Headers2BodiesMessage .= $stringHeader->name;
        //required(false) (byte)
        $this->s2Headers2BodiesMessage .= pack('C', 0);

        //string type indicator (byte)
        $headerValueData = pack('C', 2);
        //header value length (int)
        $headerValueData .= pack('n', strlen($stringHeader->value));
        //header value (works because the value is a string)
        $headerValueData .= $stringHeader->value;

        //header value length (long)
        $this->s2Headers2BodiesMessage .= pack('N', strlen($headerValueData));
        //header value
        $this->s2Headers2BodiesMessage .= $headerValueData;

        /**
         * second header (null)
         */
        //header name length (int)
        $this->s2Headers2BodiesMessage .= pack('n', strlen($nullHeader->name));
        //header name
        $this->s2Headers2BodiesMessage .= $nullHeader->name;
        //required (here true, cf constructor of $nullHeader)
        $this->s2Headers2BodiesMessage .= pack('C', 1);

        //string type indicator (byte)
        $headerValueData = pack('C', 5);
        //header value length (long)
        $this->s2Headers2BodiesMessage .= pack('N', strlen($headerValueData));
        //header value
        $this->s2Headers2BodiesMessage .= $headerValueData;

        /**
         * bodies
         */
        //number of bodies
        $this->s2Headers2BodiesMessage .= pack('n', 2);

        /**
         * first body (string)
         */
        //response uri length
        $this->s2Headers2BodiesMessage .= pack('n', 9);
        //response uri. This is responseIndex (default is "") + "/onStatus"
        $this->s2Headers2BodiesMessage .= "/onStatus";
        //response target length
        $this->s2Headers2BodiesMessage .= pack('n', 4);
        //response target. default is "null"
        $this->s2Headers2BodiesMessage .= "null";

        //result is string. byte with '2' as data type, then length, then char data
        $bodyResultsData = pack('C', 2) . pack('n', strLen($testString)) . $testString;
        //result length, long
        $this->s2Headers2BodiesMessage .= pack('N', strlen($bodyResultsData));
        //add the result itself
        $this->s2Headers2BodiesMessage .= $bodyResultsData;

        /**
         * second body (null)
         */
        //response uri length
        $this->s2Headers2BodiesMessage .= pack('n', 9);
        //response uri. This is responseIndex (default is "") + "/onStatus"
        $this->s2Headers2BodiesMessage .= "/onStatus";
        //response target length
        $this->s2Headers2BodiesMessage .= pack('n', 4);
        //response target. default is "null"
        $this->s2Headers2BodiesMessage .= "null";

        //result is null
        $bodyResultsData = pack('C', 5);
        //result length, long
        $this->s2Headers2BodiesMessage .= pack('N', strlen($bodyResultsData));
        //add the result itself
        $this->s2Headers2BodiesMessage .= $bodyResultsData;

    }

    /**
     * messages with a proper response, used to test gateway. dependant on service used. Here all will be based on mirror test, where the response data is
     * the same as the request data.
     */

    /**
     *
     */
    public function buildSimpleMirrorServiceRequestAndResponse(){

        //request

        $requestTargetURI = "MirrorService/mirrorFunction";
        $requestResponseURI = "/1";

        //version (int)
        $requestMessage = pack('n', 0);
        //number of headers (int)
        $requestMessage .= pack('n', 0);
        //number of bodies
        $requestMessage .= pack('n', 1);
        //target uri length
        $requestMessage .= pack('n', strlen($requestTargetURI));
        //target uri .
        $requestMessage .= $requestTargetURI;
        //response uri length
        $requestMessage .= pack('n', strlen($requestTargetURI));
        //response uri. 
        $requestMessage .= $requestTargetURI;

        //the function call parameters, and the returned data are the same with the mirror service.
        ////here a strict array containing a string
        //type : 0x0A
        $requestBody = pack('C', 0x0A);
        //number of sub objects on a long
        $requestBody .= pack('N', 1);

        //the contained string
        //data type is string, so use string(2)
        $requestBody .= pack('C', 2);
        //data length
        $requestBody .= pack('n', strLen("testString"));
        //data
        $requestBody .= "testString";
        $requestBodyLength = strLen($requestBody);

        //body length, long
        $requestMessage .= pack('N', $requestBodyLength);
        //add the body itself
        $requestMessage .= $requestBody;
        
        $this->mirrorServiceRequestMessage = $requestMessage;





        //response

        $responseTargetURI = "/1/onResult";
        $responseResponseURI = "null";

        //version (int)
        $responseMessage = pack('n', 0);
        //number of headers (int)
        $responseMessage .= pack('n', 0);
        //number of bodies
        $responseMessage .= pack('n', 1);
        //target uri length
        $responseMessage .= pack('n', strlen($responseTargetURI));
        //target uri .
        $responseMessage .= $responseTargetURI;
        //response uri length
        $responseMessage .= pack('n', strlen($responseResponseURI));
        //response uri.
        $responseMessage .= $responseResponseURI;

        //response body. here the string sent in the request
        //data type is string, so use string(2)
        $responseBody = pack('C', 2);
        //data length
        $responseBody .= pack('n', strLen("testString"));
        //data
        $responseBody .= "testString";
        $responseBodyLength = strLen($responseBody);

        //body length, long
        $responseMessage .= pack('N', $responseBodyLength);
        //add the body itself
        $responseMessage .= $responseBody;


        $this->mirrorServiceResponseMessage = $responseMessage;

    }


}


/**
 * used for testing with typed objects
 */
class DummyClass{
    public $data;
}
?>
