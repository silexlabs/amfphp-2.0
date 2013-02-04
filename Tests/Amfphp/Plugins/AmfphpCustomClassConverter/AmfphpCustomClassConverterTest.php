<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_CustomClassConverter
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpCustomClassConverter/AmfphpCustomClassConverter.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/CustomClasses/TestCustomClass1.php';
require_once dirname(__FILE__) . '/../../../TestData/CustomClasses/TestCustomClass2.php';

/**
 * Test class for CustomClassConverter.
 * @package Tests_Amfphp_Plugins_CustomClassConverter
 * @author Ariel Sommeria-klein
 */
class AmfphpCustomClassConverterTest extends PHPUnit_Framework_TestCase {

    /**
     * object
     * @var CustomClassConverter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $pluginConfig = array('customClassFolderPaths' => array(dirname(__FILE__) . '/../../../TestData/CustomClasses'));
        $this->object = new AmfphpCustomClassConverter($pluginConfig);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {

    }
    /**
     * test deserialiwed request
     */
    public function testfilterDeserializedRequest() {
        
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        //3 level object: TestCustomClass1, untyped, TestCustomClass2 with some data around
        $testObj1 = new stdClass();
        $testObj1->$explicitTypeField = 'TestCustomClass1';
        $testObj1->data = 'bla1';
        $subObj1 = new stdClass();
        $subObj1->data = 'bla2';
        $subObj2 = new stdClass();
        $subObj2->$explicitTypeField = 'TestCustomClass2';
        $subObj2->data = 'bla3';
        $subObj1->sub = $subObj2;
        $testObj1->sub = $subObj1;

        $testMessage = new Amfphp_Core_Amf_Message(null, null, array($testObj1));
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = $testMessage;
        $ret = $this->object->filterDeserializedRequest($testPacket);
        $modifiedPacket = $ret;
        $modifiedObj = $modifiedPacket->messages[0]->data[0];
        $this->assertEquals('TestCustomClass1', get_class($modifiedObj));
        $this->assertEquals('bla1', $modifiedObj->data);
        $this->assertEquals('stdClass', get_class($modifiedObj->sub));
        $this->assertEquals('bla2', $modifiedObj->sub->data);
        $this->assertEquals('TestCustomClass2', get_class($modifiedObj->sub->sub));
        $this->assertEquals('bla3', $modifiedObj->sub->sub->data);

        //test using a class that isn't loaded yet
        $testObj2 = new stdClass();
        $testObj2->$explicitTypeField = 'TestCustomClass3';
        $testMessage = new Amfphp_Core_Amf_Message(null, null, array($testObj2));
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = $testMessage;
        $ret = $this->object->filterDeserializedRequest($testPacket);
        $modifiedPacket = $ret;
        $modifiedObj = $modifiedPacket->messages[0]->data[0];
        $this->assertEquals('TestCustomClass3', get_class($modifiedObj));

        //test using a class that isn't available. The data should be left untouched for now
        $testObj3 = new stdClass();
        $testObj3->$explicitTypeField = 'flex.messaging.Bla';
        $testMessage = new Amfphp_Core_Amf_Message(null, null, array($testObj3));
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = $testMessage;
        $ret = $this->object->filterDeserializedRequest($testPacket);
        $modifiedPacket = $ret;
        $modifiedObj = $modifiedPacket->messages[0]->data[0];
        $this->assertEquals('flex.messaging.Bla', $modifiedObj->$explicitTypeField);


    }

    /**
     * test filter deserialized response
     */
    public function testfilterDeserializedResponse() {
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        //3 level object: TestCustomClass1, untyped, TestCustomClass2 with some data around
        $testObj1 = new TestCustomClass1();
        $testObj1->data = 'bla1';
        $subObj1 = new stdClass();
        $subObj1->data = 'bla2';
        $subObj2 = new TestCustomClass2();
        $subObj2->data = 'bla3';
        $subObj1->sub = $subObj2;
        $testObj1->sub = $subObj1;

        $testMessage = new Amfphp_Core_Amf_Message(null,null, $testObj1);
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = $testMessage;
        $ret = $this->object->filterDeserializedResponse($testPacket);
        $modifiedPacket = $ret;
        $modifiedObj = $modifiedPacket->messages[0]->data;
        $this->assertEquals('TestCustomClass1', $modifiedObj->$explicitTypeField);
        $this->assertEquals('bla1', $modifiedObj->data);
        $this->assertFalse(isset($modifiedObj->sub->$explicitTypeField));
        $this->assertEquals('bla2', $modifiedObj->sub->data);
        $this->assertEquals('TestCustomClass2', $modifiedObj->sub->sub->$explicitTypeField);
        $this->assertEquals('bla3', $modifiedObj->sub->sub->data);

        //test don't overwrite explicit type when already set
        $testObj2 = new TestCustomClass1();
        $testObj2->$explicitTypeField = 'alreadySet';
        $testMessage = new Amfphp_Core_Amf_Message(null, null, $testObj2);
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = $testMessage;
        $ret = $this->object->filterDeserializedResponse($testPacket);
        $modifiedPacket = $ret;
        $modifiedObj = $modifiedPacket->messages[0]->data;
        $this->assertEquals('alreadySet', $modifiedObj->$explicitTypeField);
    }

}

?>
