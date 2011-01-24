<?php

/**
 * testing requires some services. They are described here.
 *
 * @author Ariel Sommeria-klein
 */
class TestServicesConfig {
    public $serviceFolderPaths;
    public $serviceNames2Amfphp_Core_Common_ClassFindInfo;

    public function  __construct() {
        $this->serviceFolderPaths  = array(dirname(__FILE__) . "/services");
        $mirrorServicePath = dirname(__FILE__) . '/MirrorService.php';
        $mirrorServiceAmfphp_Core_Common_ClassFindInfo = new Amfphp_Core_Common_ClassFindInfo($mirrorServicePath, "MirrorService");
        $this->serviceNames2Amfphp_Core_Common_ClassFindInfo = array("MirrorService" => $mirrorServiceAmfphp_Core_Common_ClassFindInfo);
    }

}
?>
