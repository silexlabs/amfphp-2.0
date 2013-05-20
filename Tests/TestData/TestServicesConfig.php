<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_TestData
 */


/**
 * testing requires some services. They are described here.
 *
 * @package Tests_TestData
 * @author Ariel Sommeria-klein
 */
class TestServicesConfig extends Amfphp_Core_Config {

    /**
     * constructor
     */
    public function  __construct() {
        parent::__construct();
        $this->serviceFolderPaths  = array(dirname(__FILE__) . '/Services/' ,dirname(__FILE__) . '/MoreServices/');
        $this->serviceFolderPaths[] = array(dirname(__FILE__) . '/NamespacedServices/', '');
        $testServicePath = dirname(__FILE__) . '/TestService.php';
        $classFindInfo = new Amfphp_Core_Common_ClassFindInfo($testServicePath, 'TestService');
		//uncomment to disable baguette amf
		//$this->disabledPlugins[] = 'BaguetteAmf';
        $this->serviceNames2ClassFindInfo = array('TestService' => $classFindInfo);
    }

}
?>
