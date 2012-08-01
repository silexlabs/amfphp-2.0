<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_Logger
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpLogger/AmfphpLogger.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';

/**
 * Test class for AmfphpLogger.
 * @package Tests_Amfphp_Plugins_Logger
 * @author Ariel Sommeria-klein
 */
class AmfphpLoggerTest extends PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $logger = new AmfphpLogger();
        //delete the log file if it exists to make sure it's empty when we write to it
        try {
            unlink(AmfphpLogger::LOG_FILE_PATH);
        } catch (Exception $e) {}
        $logger->filterSerializedRequest('bla');
        $logFileContent = file_get_contents(AmfphpLogger::LOG_FILE_PATH);
        //clean up
        unlink(AmfphpLogger::LOG_FILE_PATH);

        $this->assertEquals("serialized request : \nbla\n", $logFileContent);
    }

}
