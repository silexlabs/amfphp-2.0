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
        $this->serviceFolderPaths[] = array(dirname(__FILE__) . '/NamespacedServices/', 'TestNamespace');
        $this->serviceFolderPaths[] = dirname(__FILE__) . '/../../Examples/Php/ExampleServices/';
        $testServicePath = dirname(__FILE__) . '/TestService.php';
        $classFindInfo = new Amfphp_Core_Common_ClassFindInfo($testServicePath, 'TestService');
		//uncomment to disable baguette amf
		//$this->disabledPlugins[] = 'BaguetteAmf';
        $this->serviceNames2ClassFindInfo = array('TestService' => $classFindInfo);
        $this->serviceNames2ClassFindInfo = array('TestService' => $classFindInfo);
        
        $this->pluginsConfig['AmfphpVoConverter']['voFolderPaths'] = array(AMFPHP_ROOTPATH . '/Services/Vo/', dirname(__FILE__). '/../../Examples/Php/ExampleServices/Vo/');
        
        $this->disabledPlugins[] = 'AmfphpMonitor';
        
        //My tests, shouldn't be in release code!!
        $this->pluginsFolders[] = '/Users/arielsommeria-klein/Documents/workspaces/baguetteamf/BaguetteAMF/amfphp_plugin/';
        $this->serviceFolderPaths [] = '/Users/arielsommeria-klein/Documents/workspaces/baguetteamf/test/Services/';
    }

}
?>
