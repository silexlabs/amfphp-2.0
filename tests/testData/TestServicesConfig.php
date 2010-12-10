<?php

/**
 * testing requires some services. They are described here.
 *
 * @author Ariel Sommeria-klein
 */
class TestServicesConfig {
    public $serviceFolderPaths;
    public $serviceNames2ClassFindInfo;

    public function  __construct() {
        $this->serviceFolderPaths  = array(dirname(__FILE__) . "/services");
        $mirrorServicePath = dirname(__FILE__) . '/MirrorService.php';
        $mirrorServiceClassFindInfo = new ClassFindInfo($mirrorServicePath, "MirrorService");
        $this->serviceNames2ClassFindInfo = array("MirrorService" => $mirrorServiceClassFindInfo);
    }

}
?>
