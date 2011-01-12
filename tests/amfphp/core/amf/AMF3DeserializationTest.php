<?php
/**
 * Unit tests for AMFSerializer, but using amf3
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 *
 * @author Ariel Sommeria-klein
 */

require_once dirname(__FILE__) . '/../../../../amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../testData/AMF3TestData.php';
require_once dirname(__FILE__) . '/AMFDeserializerWrapper.php';

class AMF3DeserializationTest extends PHPUnit_Framework_TestCase {

    public function testBasicMethods(){
        $testData = new AMF3TestData();

        //template
        /*
        $deserializer = new AMFDeserializerWrapper($testData->s);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->d;
        $this->assertEquals($expectedDeserialized, $deserialized);

         */

        //undefined
        $deserializer = new AMFDeserializerWrapper($testData->sUndefined);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dUndefined;
        $this->assertEquals($expectedDeserialized, $deserialized);
        
        //null
        $deserializer = new AMFDeserializerWrapper($testData->sNull);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = null;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //false
        $deserializer = new AMFDeserializerWrapper($testData->sFalse);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = false;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //true
        $deserializer = new AMFDeserializerWrapper($testData->sTrue);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = true;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //integer
        $deserializer = new AMFDeserializerWrapper($testData->sInt1);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dInt1;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AMFDeserializerWrapper($testData->sInt2);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dInt2;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //double
        $deserializer = new AMFDeserializerWrapper($testData->sDouble);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDouble;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //string
        $deserializer = new AMFDeserializerWrapper($testData->sEmptyString);
        $deserialized = $deserializer->readAMF3String();
        $expectedDeserialized = $testData->dEmptyString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AMFDeserializerWrapper($testData->sString);
        $deserialized = $deserializer->readAMF3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AMFDeserializerWrapper($testData->sStringTwice);
        $deserialized = $deserializer->readAMF3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);
        $deserialized = $deserializer->readAMF3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //xml
        $deserializer = new AMFDeserializerWrapper($testData->sXmlDocument);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dXmlDocument;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //array
        $deserializer = new AMFDeserializerWrapper($testData->sEmptyArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dEmptyArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AMFDeserializerWrapper($testData->sDenseArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDenseArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AMFDeserializerWrapper($testData->sMixedArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dMixedArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //object
        $deserializer = new AMFDeserializerWrapper($testData->sObject);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //ByteArray
        $deserializer = new AMFDeserializerWrapper($testData->sByteArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dByteArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

    }

}
?>
