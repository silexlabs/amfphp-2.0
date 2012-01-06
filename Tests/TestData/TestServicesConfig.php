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
class TestServicesConfig {
    public $serviceFolderPaths;
    public $serviceNames2ClassFindInfo;

    public function  __construct() {
        $this->serviceFolderPaths  = array(dirname(__FILE__) . '/Services/');
        $testServicePath = dirname(__FILE__) . '/TestService.php';
        $classFindInfo = new Amfphp_Core_Common_ClassFindInfo($testServicePath, 'TestService');
        $this->serviceNames2ClassFindInfo = array('TestService' => $classFindInfo);
    }

}
?>
