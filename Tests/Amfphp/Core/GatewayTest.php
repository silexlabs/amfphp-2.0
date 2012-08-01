<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../TestData/AmfTestData.php';
require_once dirname(__FILE__) . '/../../TestData/TestServicesConfig.php';

/**
 * Test class for Amfphp_Core_Gateway.
 * @package Tests_Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_GatewayTest extends PHPUnit_Framework_TestCase
{
    public function testService()
    {
        $amfTestData = new AmfTestData();
        $config = new Amfphp_Core_Config();
        $testServiceConfig = new TestServicesConfig();
        $config->serviceFolderPaths = $testServiceConfig->serviceFolderPaths;
        $config->serviceNames2ClassFindInfo = $testServiceConfig->serviceNames2ClassFindInfo;
        $gateway = new Amfphp_Core_Gateway(array(), array(), $amfTestData->testServiceRequestPacket, Amfphp_Core_Amf_Constants::CONTENT_TYPE, $config);
        $ret = $gateway->service();
        $this->assertEquals(bin2hex($amfTestData->testServiceResponsePacket), bin2hex($ret));
    }

    public function testEmptyMessage()
    {
        $config = new Amfphp_Core_Config();
        $gateway = new Amfphp_Core_Gateway(array(), array(), array(), Amfphp_Core_Amf_Constants::CONTENT_TYPE, $config);
        $ret = $gateway->service();

        $this->assertTrue(strpos($ret, 'service') != false);

    }

}
