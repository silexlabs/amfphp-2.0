<?php
/**
 * Unit tests for Amfphp_Core_Amf_Serializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/AmfTestData.php';
require_once dirname(__FILE__) . '/AmfDeserializerWrapper.php';

class DeserializationTest extends PHPUnit_Framework_TestCase{


    public function testBasicMethods(){
        $testData = new AmfTestData();

        //readByte
        $deserializer = new AmfDeserializerWrapper($testData->sByte);
        $deserialized = $deserializer->readByte();
        $expectedDeserialized = $testData->dByte;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readInt
        $deserializer = new AmfDeserializerWrapper($testData->sInt);
        $deserialized = $deserializer->readInt();
        $expectedDeserialized = $testData->dInt;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readLong
        $deserializer = new AmfDeserializerWrapper($testData->sLong);
        $deserialized = $deserializer->readLong();
        $expectedDeserialized = $testData->dLong;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readDouble
        $deserializer = new AmfDeserializerWrapper($testData->sDouble);
        $deserialized = $deserializer->readDouble();
        $expectedDeserialized = $testData->dDouble;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readUtf
        $deserializer = new AmfDeserializerWrapper($testData->sUtf);
        $deserialized = $deserializer->readUtf();
        $expectedDeserialized = $testData->dUtf;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readLongUtf
        $deserializer = new AmfDeserializerWrapper($testData->sLongUtf);
        $deserialized = $deserializer->readLongUtf();
        $expectedDeserialized = $testData->dLongUtf;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //read Number
        $deserializer = new AmfDeserializerWrapper($testData->sNumber);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dNumber;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //read Boolean  
        $deserializer = new AmfDeserializerWrapper($testData->sBoolean);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dBoolean;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readString (short string)
        $deserializer = new AmfDeserializerWrapper($testData->sString);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readArray (Object)
        $deserializer = new AmfDeserializerWrapper($testData->sObject);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readObject();
        $expectedDeserialized = $testData->dObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readNull
        $deserializer = new AmfDeserializerWrapper($testData->sNull);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dNull;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readUndefined
        $deserializer = new AmfDeserializerWrapper($testData->sUndefined);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dUndefined;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readReference
        $deserializer = new AmfDeserializerWrapper($testData->sReference);
        $deserialized = $deserializer->readReference();
        $expectedDeserialized = $testData->dReference;
        //TODO better tests for references
        //$this->assertEquals($expectedDeserialized, $deserialized);

        //readArray (EcmaArray)
        $deserializer = new AmfDeserializerWrapper($testData->sEcmaArray);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dEcmaArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readObjectEnd
        //nothing!
        //
        //readArray (strict array)
        $deserializer = new AmfDeserializerWrapper($testData->sStrictArray);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dStrictArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readDate
        $deserializer = new AmfDeserializerWrapper($testData->sDate);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dDate;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readString (long string)
        $deserializer = new AmfDeserializerWrapper($testData->sLongString);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dLongString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readXml
        $deserializer = new AmfDeserializerWrapper($testData->sXml);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dXml;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //readTypedObject
        $deserializer = new AmfDeserializerWrapper($testData->sTypedObject);
        $type = $deserializer->readByte();
        $deserialized = $deserializer->readData($type);
        $expectedDeserialized = $testData->dTypedObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        

    }

    public function testSerializingPackets(){
        $testData = new AmfTestData();
        /*
        template

        //Packet with

         */

        //Packet with null header
        $deserializer = new Amfphp_Core_Amf_Deserializer($testData->sNullHeaderPacket);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dNullHeaderPacket;
        $this->assertEquals($expectedDeserialized, $deserialized);


        //Packet with string header
        $deserializer = new AmfDeserializerWrapper($testData->sStringHeaderPacket);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dStringHeaderPacket;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //Packet with null Message
        $deserializer = new AmfDeserializerWrapper($testData->sNullMessagePacket);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dNullMessagePacket;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //Packet with string Message
        $deserializer = new AmfDeserializerWrapper($testData->sStringMessagePacket);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->dStringMessagePacket;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //Packet with 2 headers and 2 Messages
        $deserializer = new AmfDeserializerWrapper($testData->s2Headers2MessagesPacket);
        $deserialized = $deserializer->deserialize();
        $expectedDeserialized = $testData->d2Headers2MessagesPacket;
        $this->assertEquals($expectedDeserialized, $deserialized);

    }
    


}
?>
