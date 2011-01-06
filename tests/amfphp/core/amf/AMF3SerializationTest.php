<?php
/**
 * Unit tests for AMFSerializer, but using amf3
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMF3TestData.php';
require_once dirname(__FILE__) . '/AMFSerializerWrapper.php';

class AMF3SerializationTest extends PHPUnit_Framework_TestCase {

    public function testBasicMethods(){
        $testData = new AMF3TestData();

        $emptyMessage = new AMFMessage();
        $emptyMessage->amfVersion = 3;
        /*
         template

        //write
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->write($testData->d);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->s;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

         */
        //array
 /*       $nestedAssoc = unserialize(file_get_contents("../../../testData/nestedAssocArray.txt"));
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Array($nestedAssoc);
        $serialized = $serializer->getOutput();
        $expectedSerialized = 0;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));
*/

        //undefined
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Undefined();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sUndefined;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //null
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Null();
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sNull;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //false
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Bool(false);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sFalse;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //true
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Bool(true);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sTrue;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //integer
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Number($testData->dInt1);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt1;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Number($testData->dInt2);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sInt2;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //double
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Number($testData->dDouble);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDouble;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //string
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3String($testData->dEmptyString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEmptyString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3String($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sString;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3String($testData->dString);
        $serializer->writeAMF3String($testData->dString);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sStringTwice;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //xml
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3XML($testData->dXmlDocument);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sXmlDocument;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //array
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Array($testData->dEmptyArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sEmptyArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Array($testData->dDenseArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sDenseArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Array($testData->dMixedArray);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sMixedArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //object
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3Object($testData->dObject);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sObject;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));

        //ByteArray
        $serializer = new AMFSerializerWrapper($emptyMessage, false);
        $serializer->writeAMF3ByteArray($testData->dByteArray->data);
        $serialized = $serializer->getOutput();
        $expectedSerialized = $testData->sByteArray;
        $this->assertEquals(bin2hex($expectedSerialized), bin2hex($serialized));


    }

}
?>
