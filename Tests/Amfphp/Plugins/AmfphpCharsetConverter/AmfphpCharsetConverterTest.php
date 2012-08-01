<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_CharsetConverter
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpCharsetConverter/AmfphpCharsetConverter.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';

/**
 * Test class for CharsetConverter.
 * @package Tests_Amfphp_Plugins_CharsetConverter
 * @author Ariel Sommeria-klein
 *
 */
class AmfphpCharsetConverterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var CharsetConverter
     */
    protected $object;

    protected $textInClientCharset;

    protected $textInPhpCharset;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $pluginConfig = array('clientCharset' => 'UTF-8', 'phpCharset' => 'ISO-8859-1', 'method' =>AmfphpCharsetConverter::METHOD_ICONV);
        $this->object = new AmfphpCharsetConverter($pluginConfig);
        $this->textInClientCharset = 'éèê'; //utf-8
        $this->textInPhpCharset = iconv('UTF-8', 'ISO-8859-1', $this->textInClientCharset);

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testRequestDeserializedFilter()
    {
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = new Amfphp_Core_Amf_Message(null, null, $this->textInClientCharset);
        $ret = $this->object->filterDeserializedRequest($testPacket);
        $modifiedPacket = $ret;
        $this->assertEquals($this->textInPhpCharset, $modifiedPacket->messages[0]->data);
    }

    public function testResponseDeserializedFilter()
    {
        $testPacket = new Amfphp_Core_Amf_Packet();
        $testPacket->messages[] = new Amfphp_Core_Amf_Message(null, null, $this->textInPhpCharset);
        $ret = $this->object->filterDeserializedResponse($testPacket);
        $modifiedPacket = $ret;
        $this->assertEquals($this->textInClientCharset, $modifiedPacket->messages[0]->data);
    }

}
