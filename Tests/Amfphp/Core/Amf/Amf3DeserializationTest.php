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
require_once dirname(__FILE__) . '/../../../TestData/Amf3TestData.php';
require_once dirname(__FILE__) . '/AmfDeserializerWrapper.php';
/**
 * Unit tests for Amfphp_Core_Amf_Serializer, but using amf3
 * note: phpunit dataProvider mechanism doesn't work well, so lots of boiler plate code here. Oh well... A.S.
 * 
 * @package Tests_Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */
class Amf3DeserializationTest extends PHPUnit_Framework_TestCase {
    /**
     * test basic methods
     */
    public function testBasicMethods(){
        $testData = new Amf3TestData();

        //template
        /*
        $deserializer = new AmfDeserializerWrapper($testData->s);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->d;
        $this->assertEquals($expectedDeserialized, $deserialized);

         */

        //undefined
        $deserializer = new AmfDeserializerWrapper($testData->sUndefined);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dUndefined;
        $this->assertEquals($expectedDeserialized, $deserialized);
        
        //null
        $deserializer = new AmfDeserializerWrapper($testData->sNull);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = null;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //false
        $deserializer = new AmfDeserializerWrapper($testData->sFalse);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = false;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //true
        $deserializer = new AmfDeserializerWrapper($testData->sTrue);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = true;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //integer
        $deserializer = new AmfDeserializerWrapper($testData->sInt1);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dInt1;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sInt2);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dInt2;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //double
        $deserializer = new AmfDeserializerWrapper($testData->sDouble);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDouble;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //string
        $deserializer = new AmfDeserializerWrapper($testData->sEmptyString);
        $deserialized = $deserializer->readAmf3String();
        $expectedDeserialized = $testData->dEmptyString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sString);
        $deserialized = $deserializer->readAmf3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sStringTwice);
        $deserialized = $deserializer->readAmf3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);
        $deserialized = $deserializer->readAmf3String();
        $expectedDeserialized = $testData->dString;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //xml
        $deserializer = new AmfDeserializerWrapper($testData->sXmlDocument);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dXmlDocument;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sDate);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDate;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //array
        $deserializer = new AmfDeserializerWrapper($testData->sEmptyArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dEmptyArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sDenseArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDenseArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        $deserializer = new AmfDeserializerWrapper($testData->sMixedArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dMixedArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //object
        $deserializer = new AmfDeserializerWrapper($testData->sObject);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //ByteArray
        $deserializer = new AmfDeserializerWrapper($testData->sByteArray);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dByteArray;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //VectorInt
        $deserializer = new AmfDeserializerWrapper($testData->sVectorInt);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dVectorInt;
        $this->assertEquals($expectedDeserialized, $deserialized);
        
        //VectorUint
        $deserializer = new AmfDeserializerWrapper($testData->sVectorUint);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dVectorUint;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //VectorDouble
        $deserializer = new AmfDeserializerWrapper($testData->sVectorDouble);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dVectorDouble;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //VectorObject
        $deserializer = new AmfDeserializerWrapper($testData->sVectorObject);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dVectorObject;
        $this->assertEquals($expectedDeserialized, $deserialized);

        //Dictionary
        /* not supported
        $deserializer = new AmfDeserializerWrapper($testData->sDictionary);
        $deserialized = $deserializer->readAmf3Data();
        $expectedDeserialized = $testData->dDictionary;
        $this->assertEquals($expectedDeserialized, $deserialized);
         * 
         */

    }

}
?>
