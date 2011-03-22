<?php
/*
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * Unit tests for Amfphp_Core_Amf_Serializer, but using amf3
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @package Tests_Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/Amf3TestData.php';
require_once dirname(__FILE__) . '/AmfSerializerWrapper.php';

class Amf3SerializationTest extends PHPUnit_Framework_TestCase {

    public function testBasicMethods(){
        $testData = new Amf3TestData();

        $emptyPacket = new Amfphp_Core_Amf_Packet();
        $emptyPacket->amfVersion = 3;
        /*
         template

        //write
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->write($testData->d);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */
 
        //undefined
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Undefined();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUndefined;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //null
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Null();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNull;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //false
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Bool(false);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sFalse;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //true
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Bool(true);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sTrue;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //integer
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Number($testData->dInt1);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt1;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Number($testData->dInt2);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt2;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //double
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Number($testData->dDouble);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDouble;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //string
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3String($testData->dEmptyString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEmptyString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3String($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3String($testData->dString);
        $serializer->writeAmf3String($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sStringTwice;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //xml
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3XML($testData->dXmlDocument);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sXmlDocument;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //array
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Array($testData->dEmptyArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEmptyArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Array($testData->dDenseArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDenseArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Array($testData->dMixedArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sMixedArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //object
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3Object($testData->dObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //ByteArray
        $serializer = new AmfSerializerWrapper($emptyPacket);
        $serializer->writeAmf3ByteArray($testData->dByteArray->data);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByteArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));


    }

}
?>
