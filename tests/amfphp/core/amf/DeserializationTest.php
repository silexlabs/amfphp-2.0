<?php
/**
 * Unit tests for AMFSerializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMFTestData.php';
require_once dirname(__FILE__) . '/AMFDeserializerWrapper.php';

class DeserializationTest extends PHPUnit_Framework_TestCase{


    public function testBasicMethods(){
        $testData = new AMFTestData();

        //readByte
        $deserializer = new AMFDeserializerWrapper($testData->sByte);
        $deserialized = $deserializer->readByte();
        $expectedDeserialized = $testData->dByte;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readInt
        $deserializer = new AMFDeserializerWrapper($testData->sInt);
        $deserialized = $deserializer->readInt();
        $expectedDeserialized = $testData->dInt;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readLong
        $deserializer = new AMFDeserializerWrapper($testData->sLong);
        $deserialized = $deserializer->readLong();
        $expectedDeserialized = $testData->dLong;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readDouble
        $deserializer = new AMFDeserializerWrapper($testData->sDouble);
        $deserialized = $deserializer->readDouble();
        $expectedDeserialized = $testData->dDouble;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readUtf
        $deserializer = new AMFDeserializerWrapper($testData->sUtf);
        $deserialized = $deserializer->readUtf();
        $expectedDeserialized = $testData->dUtf;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readLongUtf
        $deserializer = new AMFDeserializerWrapper($testData->sLongUtf);
        $deserialized = $deserializer->readLongUtf();
        $expectedDeserialized = $testData->dLongUtf;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //read Number
        $deserializer = new AMFDeserializerWrapper($testData->sNumber);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dNumber;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //read Boolean  
        $deserializer = new AMFDeserializerWrapper($testData->sBoolean);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dBoolean;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readString (short string)
        $deserializer = new AMFDeserializerWrapper($testData->sString);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readArray (Object)
        $deserializer = new AMFDeserializerWrapper($testData->sObject);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readObject();
        $expectedDeserialized = $testData->dObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readNull
        $deserializer = new AMFDeserializerWrapper($testData->sNull);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dNull;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readUndefined
        $deserializer = new AMFDeserializerWrapper($testData->sUndefined);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dUndefined;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readReference
        $deserializer = new AMFDeserializerWrapper($testData->sReference);
        $deserialized = $deserializer->readReference();
        $expectedDeserialized = $testData->dReference;
        //TODO better tests for references
        //$this->assertEquals($expectedDeserialized, $deserialized);

        //readArray (EcmaArray)
        $deserializer = new AMFDeserializerWrapper($testData->sEcmaArray);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dEcmaArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readObjectEnd
        //nothing!
        //
        //readArray (strict array)
        $deserializer = new AMFDeserializerWrapper($testData->sStrictArray);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dStrictArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readDate
        $deserializer = new AMFDeserializerWrapper($testData->sDate);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dDate;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readString (long string)
        $deserializer = new AMFDeserializerWrapper($testData->sLongString);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dLongString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readXml
        $deserializer = new AMFDeserializerWrapper($testData->sXml);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dXml;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readTypedObject
        $deserializer = new AMFDeserializerWrapper($testData->sTypedObject);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dTypedObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        

    }

    public function testSerializingMessages(){
        $testData = new AMFTestData();
        /*
        template

        //message with

         */

        //message with null header
        $deserializer = new AMFDeserializer($testData->sNullHeaderMessage);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dNullHeaderMessage;
        $this->assertEquals($expectedDeserialized, $deserialized);


        //message with string header
        $deserializer = new AMFDeserializerWrapper($testData->sStringHeaderMessage);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dStringHeaderMessage;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //message with null body
        $deserializer = new AMFDeserializerWrapper($testData->sNullBodyMessage);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dNullBodyMessage;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //message with string body
        $deserializer = new AMFDeserializerWrapper($testData->sStringBodyMessage);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dStringBodyMessage;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //message with 2 headers and 2 bodies
        $deserializer = new AMFDeserializerWrapper($testData->s2Headers2BodiesMessage);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->d2Headers2BodiesMessage;
        $this->assertEquals($expectedDeserialized, $deserialized);

    }
    


}
?>
