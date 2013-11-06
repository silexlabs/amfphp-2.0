<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_ErrorHandler
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpErrorHandler/AmfphpErrorHandler.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';

/**
 * Test class for AmfphpErrorHandler.
 * @package Tests_Amfphp_Plugins_ErrorHandler
 * @author Ariel Sommeria-klein
 */
class AmfphpErrorHandlerTest extends PHPUnit_Framework_TestCase {

    /**
     * object
     * @var AmfphpErrorHandler
     */
    protected $object;
    

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        error_reporting(E_ALL ^ E_USER_NOTICE );
        $this->object = new AmfphpErrorHandler();
        
    }
    
    protected function tearDown(){
        error_reporting(E_ALL);
        
    }
    

    /**
     * test trigger error with error level => should throw exception
     * @expectedException Amfphp_Core_Exception
     * */
    public function testThrows(){
        trigger_error("oops", E_USER_ERROR);
    }

    /**
     * test trigger error with notice level => should not throw exception, as in set up notices are ignored
     * */
    public function testDoesntThrow(){
        trigger_error("oops", E_USER_NOTICE);
        $this->assertTrue(true);
    }

}

?>
