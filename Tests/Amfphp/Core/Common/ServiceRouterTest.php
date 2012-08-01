<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core_Common
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/AmfTestData.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/TestServicesConfig.php';

/**
 * Test class for Amfphp_Core_Common_ServiceRouter.
 * @package Tests_Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ServiceRouterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Amfphp_Core_Common_ServiceRouter
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $testServiceConfig = new TestServicesConfig();
        $this->object = new Amfphp_Core_Common_ServiceRouter($testServiceConfig->serviceFolderPaths, $testServiceConfig->serviceNames2ClassFindInfo);
    }

    public function testExecuteTestServiceCall()
    {
        //return one param
        $testParamsArray = array('a');
        $mirrored = $this->object->executeServiceCall('TestService', 'returnOneParam', $testParamsArray);
        $this->assertEquals($mirrored, 'a');

        // return sum
        $testParamsArray = array(1, 2);
        $mirrored = $this->object->executeServiceCall('TestService', 'returnSum', $testParamsArray);
        $this->assertEquals($mirrored, 3);
    }

    public function testFindDummyServiceInFolder()
    {
        $ret = $this->object->executeServiceCall('DummyService', 'returnNull', array());
        $this->assertEquals($ret, null);
    }

     /**
     * @expectedException Amfphp_Core_Exception
     */
    public function testNoServiceException()
    {
        $ret = $this->object->executeServiceCall('NoService', 'noFunction', array());
    }

     /**
     * @expectedException Amfphp_Core_Exception
     */
    public function testNoFunctionException()
    {
        $ret = $this->object->executeServiceCall('DummyService', 'noFunction', array());
        $this->assertEquals($ret, null);
    }

     /**
     * @expectedException Amfphp_Core_Exception
     */
    public function testReservedMethodException()
    {
        $ret = $this->object->executeServiceCall('DummyService', '_reserved', array());
    }
}
