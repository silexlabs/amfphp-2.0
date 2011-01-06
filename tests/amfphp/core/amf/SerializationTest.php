<?php
/**
 * Unit tests for AMFSerializer
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMFTestData.php';
require_once dirname(__FILE__) . '/AMFSerializerWrapper.php';

class SerializationTest extends PHPUnit_Framework_TestCase{


    public function testBasicMethods(){
        $testData = new AMFTestData();

        /*
         template

        //write
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->write($testData->d);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        $emptyMessage = new AMFMessage();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByte;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeByte
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeByte($testData->dByte);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByte;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeInt
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeInt($testData->dInt);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLong
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeLong($testData->dLong);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLong;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDouble
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeDouble($testData->dDouble);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDouble;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUtf
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeUtf($testData->dUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeLongUtf
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeLongUtf($testData->dLongUtf);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongUtf;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNumber
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeNumber($testData->dNumber);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNumber;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeBoolean
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeBoolean($testData->dBoolean);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sBoolean;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (short string)
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeString($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (Object)
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeArray($testData->dObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeNull
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeNull();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNull;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeUndefined
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeUndefined($testData->dUndefined);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUndefined;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeReference
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeReference($testData->dReference);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sReference;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (EcmaArray)
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeArray($testData->dEcmaArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEcmaArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeObjectEnd
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeObjectEnd();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObjectEnd;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeArray (strict array)
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeArray($testData->dStrictArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sStrictArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeDate
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeDate($testData->dDate);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDate;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeString (long string)
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeString($testData->dLongString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sLongString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeXml
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeXml($testData->dXml);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sXml;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //writeTypedObject
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeTypedObject($testData->dTypedObject, get_class($testData->dTypedObject));
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sTypedObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

    }

    public function testSerializingMessages(){
        $testData = new AMFTestData();
        /*
        template

        //message with
        $serializer = new AMFSerializerWrapper($testData->d, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */

        //message with null header
        $serializer = new AMFSerializerWrapper($testData->dNullHeaderMessage, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sNullHeaderMessage;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //message with string header
        $serializer = new AMFSerializerWrapper($testData->dStringHeaderMessage, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sStringHeaderMessage;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //message with null body
        $serializer = new AMFSerializerWrapper($testData->dNullBodyMessage, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sNullBodyMessage;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //message with string body
        $serializer = new AMFSerializerWrapper($testData->dStringBodyMessage, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->sStringBodyMessage;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //message with 2 headers and 2 bodies
        $serializer = new AMFSerializerWrapper($testData->d2Headers2BodiesMessage, false);
        $serialized = $serializer->serialize();
        $expectedSerialized = $testData->s2Headers2BodiesMessage;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));


    }
    


}
?>
