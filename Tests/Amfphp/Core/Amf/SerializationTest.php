<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core_Amf
 */
/**
 *  includes
 *  */
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/AmfTestData.php';
require_once dirname(__FILE__) . '/AmfSerializerWrapper.php';

/**
 * Unit tests for Amfphp_Core_Amf_Serializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @package Tests_Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */
class SerializationTest extends PHPUnit_Framework_TestCase {

    /**
     * test basic methods
     */
    public function testBasicMethods() {
        $testData = new AmfTestData();
        $emptyPacket = new Amfphp_Core_Amf_Packet();

        /*
          template

          //write
          $serializer = new AmfSerializerWrapper($emptyPacket);
          $serializer->write($testData->d);
          $serialized = $serializer->getOutput();
          $expectedSerialized = $testData->s;
          $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        //writeByte
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeByte($testData->dByte);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByte;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeInt
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeInt($testData->dInt);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLong
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeLong($testData->dLong);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLong;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDouble
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeDouble($testData->dDouble);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDouble;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUtf
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeUtf($testData->dUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLongUtf
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeLongUtf($testData->dLongUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNumber
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeNumber($testData->dNumber);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNumber;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeBoolean
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeBoolean($testData->dBoolean);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sBoolean;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (short string)
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeString($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArrayOrObject (Object)
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeArrayOrObject($testData->dObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNull
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeNull();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNull;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUndefined
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeUndefined($testData->dUndefined);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUndefined;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeReference
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeReference($testData->dReference);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sReference;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArrayOrObject (EcmaArray)
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeArrayOrObject($testData->dEcmaArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEcmaArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeObjectEnd
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeObjectEnd();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObjectEnd;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArrayOrObject (strict array)
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeArrayOrObject($testData->dStrictArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sStrictArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDate
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeDate($testData->dDate);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDate;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (long string)
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeString($testData->dLongString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeXml
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeXml($testData->dXml);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sXml;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeTypedObject
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeTypedObject($testData->dTypedObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sTypedObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));
    }

    /**
     * test serializing packets
     */
    public function testSerializingPackets() {
        $testData = new AmfTestData();
        /*
          template

          //Packet with
          $serializer = new AmfSerializerWrapper($testData->d);
          $serialized = $serializer->serialize();
          $expectedSerialized = $testData->s;
          $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        //Packet with null header
        $serializer = new AmfSerializerWrapper($testData->dNullHeaderPacket);
        $serialized = $serializer->serialize($testData->dNullHeaderPacket);
        $expectedSerialized = $testData->sNullHeaderPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with string header
        $serializer = new AmfSerializerWrapper($testData->dStringHeaderPacket);
        $serialized = $serializer->serialize($testData->dStringHeaderPacket);
        $expectedSerialized = $testData->sStringHeaderPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with null Message
        $serializer = new AmfSerializerWrapper($testData->dNullMessagePacket);
        $serialized = $serializer->serialize($testData->dNullMessagePacket);
        $expectedSerialized = $testData->sNullMessagePacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with string Message
        $serializer = new AmfSerializerWrapper($testData->dStringMessagePacket);
        $serialized = $serializer->serialize($testData->dStringMessagePacket);
        $expectedSerialized = $testData->sStringMessagePacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //Packet with 2 headers and 2 Messages
        $serializer = new AmfSerializerWrapper($testData->d2Headers2MessagesPacket);
        $serialized = $serializer->serialize($testData->d2Headers2MessagesPacket);
        $expectedSerialized = $testData->s2Headers2MessagesPacket;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));
    }

}

?>
