<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Plugins_Monitor
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/../../../../Amfphp/Plugins/AmfphpMonitor/AmfphpMonitor.php';
require_once dirname(__FILE__) . '/../../../../Amfphp/ClassLoader.php';

/**
 * Test class for Monitor.
 * @package Tests_Amfphp_Plugins_Monitor
 * @author Ariel Sommeria-klein
 */
class AmfphpMonitorTest extends PHPUnit_Framework_TestCase {

    /**
     * object
     * @var Monitor
     */
    protected $object;
    
    protected $logPath;
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->logPath = dirname(__FILE__). '/testlog.txt.php';
        $pluginConfig['maxLogFileSize'] = 20;
        $pluginConfig['logPath'] = $this->logPath;
        file_put_contents($this->logPath, '<?php exit();?>');
        $this->object = new AmfphpMonitor($pluginConfig);
    }

    
    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
       file_put_contents($this->logPath, "<?php exit();?>");
    }
    /**
     * test basic logging
     * for test to work must give permission to write to test log file first
     */
    public function testLog() {
        AmfphpMonitor::addTime('test time');
        $this->object->filterSerializedResponse(null);
        $log = file_get_contents($this->logPath);
        $this->assertTrue(strpos($log, 'test time') !== false);
    }
    
    /**
     * test no write permission. No error should be thrown, so the only thing to test is that there is no error/exception
     */
    public function testNoPermission(){
        $pluginConfig['logPath'] = $this->logPath. 'bla';
        $this->object = new AmfphpMonitor($pluginConfig);
        
    }
    
    /**
     * test file writing stops at max size. max size is set to 10 in setUp, so there is only space for 1 log
     * @todo doesn't work as fileSize always returns the same value as at first read. So test manually instead.
     */
    public function disabletestMaxSize(){
        AmfphpMonitor::addTime('test time 0 ');
        $this->object->filterSerializedResponse(null);
        AmfphpMonitor::addTime('should not log');
        $this->object->filterSerializedResponse(null);
        
        $this->object->filterSerializedResponse(null);
        
        $this->object->filterSerializedResponse(null);
        
        $this->object->filterSerializedResponse(null);
        
        $log = file_get_contents($this->logPath);
        //echo $log;
        //echo "ertert " . filesize($this->logPath);
        $this->assertTrue(strpos($log, 'should not log') === false);
        
    }
}

?>
