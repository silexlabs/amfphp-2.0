<?php
/**
 *  This file part is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */


/**
 * testing requires some services. They are described here.
 *
 * @author Ariel Sommeria-klein
 */
class TestServicesConfig {
    public $serviceFolderPaths;
    public $serviceNames2ClassFindInfo;

    public function  __construct() {
        $this->serviceFolderPaths  = array(dirname(__FILE__) . "/Services");
        $mirrorServicePath = dirname(__FILE__) . '/MirrorService.php';
        $mirrorServiceAmfphp_Core_Common_ClassFindInfo = new Amfphp_Core_Common_ClassFindInfo($mirrorServicePath, "MirrorService");
        $this->serviceNames2ClassFindInfo = array("MirrorService" => $mirrorServiceAmfphp_Core_Common_ClassFindInfo);
    }

}
?>
