<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_FlexMessaging
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpFlexMessaging/AmfphpFlexMessaging.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';
require_once dirname(__FILE__) . '/../../../TestData/TestServicesConfig.php';

/**
 * Test class for FlexMessaging.
 * @package Tests_Amfphp_Plugins_FlexMessaging
 * @author Ariel Sommeria-klein
 */
class AmfphpFlexMessagingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var FlexMessaging
     */
    protected $object;

    /**
     *
     * @var Amfphp_Core_Common_ServiceRouter
     */
    protected $serviceRouter;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new AmfphpFlexMessaging();
        $testServiceConfig = new TestServicesConfig();
        $this->serviceRouter = new Amfphp_Core_Common_ServiceRouter($testServiceConfig->serviceFolderPaths, $testServiceConfig->serviceNames2ClassFindInfo);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testGetAmfRequestMessageHandlerFilter()
    {
        $requestMessage = new Amfphp_Core_Amf_Message(null, '/1', null);
        $requestMessage->data = array();
        $command = new stdClass();
        $command->_explicitType = AmfphpFlexMessaging::FLEX_TYPE_COMMAND_MESSAGE;
        $command->messageId = '690D76D5-13C0-DFBD-7F2F-9E3786B59EB5';
        $requestMessage->data[] = $command;
        $ret = $this->object->filterAmfRequestMessageHandler(null, $requestMessage);
        $this->assertEquals($this->object, $ret);
    }

    public function testCommandMessage()
    {
        $requestMessage = new Amfphp_Core_Amf_Message(null, '/1', null);
        $requestMessage->data = array();
        $expectedResponseMessage = new Amfphp_Core_Amf_Message('/1/onResult', null, null);

        $command = new stdClass();
        $command->_explicitType = AmfphpFlexMessaging::FLEX_TYPE_COMMAND_MESSAGE;
        $command->messageId = '690D76D5-13C0-DFBD-7F2F-9E3786B59EB5';
        $requestMessage->data[] = $command;
        $responseMessage = $this->object->handleRequestMessage($requestMessage, $this->serviceRouter);
        $expectedAcknowledge = new AmfphpFlexMessaging_AcknowledgeMessage('690D76D5-13C0-DFBD-7F2F-9E3786B59EB5');
        //copy random ids, so as not to fail test
        $expectedAcknowledge->clientId = $responseMessage->data->clientId;
        $expectedAcknowledge->messageId = $responseMessage->data->messageId;
        $expectedResponseMessage->data = $expectedAcknowledge;
        $this->assertEquals(print_r($expectedResponseMessage, true), print_r($responseMessage, true));
    }

    public function testRemotingMessage()
    {
        $requestMessage = new Amfphp_Core_Amf_Message(null, '/1', null);
        $requestMessage->data = array();
        $expectedResponseMessage = new Amfphp_Core_Amf_Message('/1/onResult', null, null);

        $remoting = new stdClass();
        $remoting->_explicitType = AmfphpFlexMessaging::FLEX_TYPE_REMOTING_MESSAGE;
        $remoting->messageId = '690D76D5-13C0-DFBD-7F2F-9E3786B59EB5';
        $remoting->source = 'TestService';
        $remoting->operation = 'returnOneParam';
        $remoting->body = array('boo');
        $requestMessage->data[] = $remoting;
        $responseMessage = $this->object->handleRequestMessage($requestMessage, $this->serviceRouter);
        $expectedAcknowledge = new AmfphpFlexMessaging_AcknowledgeMessage('690D76D5-13C0-DFBD-7F2F-9E3786B59EB5');
        //copy random ids, so as not to fail test
        $expectedAcknowledge->clientId = $responseMessage->data->clientId;
        $expectedAcknowledge->messageId = $responseMessage->data->messageId;
        $expectedAcknowledge->body = 'boo';
        $expectedResponseMessage->data = $expectedAcknowledge;
        $this->assertEquals(print_r($expectedResponseMessage, true), print_r($responseMessage, true));

        //test error handling. Must reuse the same AmfphpFlexMessaging obj so that it already has data
        $responsePacket = $this->object->generateErrorResponse(new Exception('Bad!'), $requestMessage, null);
        $expectedError = new AmfphpFlexMessaging_ErrorMessage('690D76D5-13C0-DFBD-7F2F-9E3786B59EB5');
        $expectedResponseMessage->data = $expectedError;
        $this->assertEquals('AmfphpFlexMessaging_ErrorMessage', get_class($responsePacket->messages[0]->data));

    }

}
