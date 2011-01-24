<?php
/**
 * Unit tests for Amfphp_Core_Amf_Serializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/AMFPHPClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMFTestData.php';
require_once dirname(__FILE__) . '/AMFSerializerWrapper.php';

class SerializationTest extends PHPUnit_Framework_TestCase{


    public function testBasicMethods(){
        $testData = new AMFTestData();
        $emptyPacket = new Amfphp_Core_Amf_Packet();

        /*
         template

        //write
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->write($testData->d);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        //writeByte
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeByte($testData->dByte);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByte;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeInt
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeInt($testData->dInt);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLong
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeLong($testData->dLong);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLong;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDouble
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeDouble($testData->dDouble);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDouble;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUtf
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeUtf($testData->dUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLongUtf
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeLongUtf($testData->dLongUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNumber
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeNumber($testData->dNumber);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNumber;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeBoolean
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeBoolean($testData->dBoolean);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sBoolean;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (short string)
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeString($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (Object)
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeArray($testData->dObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNull
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeNull();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNull;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUndefined
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeUndefined($testData->dUndefined);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUndefined;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeReference
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeReference($testData->dReference);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sReference;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (EcmaArray)
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeArray($testData->dEcmaArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEcmaArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeObjectEnd
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeObjectEnd();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObjectEnd;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (strict array)
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeArray($testData->dStrictArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sStrictArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDate
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeDate($testData->dDate);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDate;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (long string)
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeString($testData->dLongString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeXml
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeXml($testData->dXml);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sXml;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeTypedObject
        $serializer = new AMFSerializerWrapper($emptyPacket);
        $serializer->writeTypedObject($testData->dTypedObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sTypedObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

    }

    public function testSerializingPackets(){
        $testData = new AMFTestData();
        /*
        template

        //Packet with
        $serializer = new AMFSerializerWrapper($testData->d);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        //Packet with null header
        $serializer = new AMFSerializerWrapper($testData->dNullHeaderPacket);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sNullHeaderPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with string header
        $serializer = new AMFSerializerWrapper($testData->dStringHeaderPacket);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sStringHeaderPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with null Message
        $serializer = new AMFSerializerWrapper($testData->dNullMessagePacket);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sNullMessagePacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with string Message
        $serializer = new AMFSerializerWrapper($testData->dStringMessagePacket);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sStringMessagePacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with 2 headers and 2 Messages
        $serializer = new AMFSerializerWrapper($testData->d2Headers2MessagesPacket);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->s2Headers2MessagesPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));


    }
    


}
?>
