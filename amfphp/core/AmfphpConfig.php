<?php

/**
 * responsable for loading and maintaining AMFPHP configuration
 *
 * @author Ariel Sommeria-klein
 */
class AmfphpConfig {

    /**
     * paths to folders containing services(relative or absolute)
     * @var <array> of paths
     */
    public $serviceFolderPaths;

    /**
     * a dictionary of service classes represented in a Amfphp_Core_Common_ClassFindInfo.
     * The key is the name of the service, the value is the class find info.
     * for example: AmfphpDiscoveryService -> new Amfphp_Core_Common_ClassFindInfo( ... /plugins/serviceBrowser/AmfphpDiscoveryService.php, AmfphpDiscoveryService)
     * The forward slash is important, don't use '\'!     
     * @var <array> of Amfphp_Core_Common_ClassFindInfo
     */
    public $serviceNames2Amfphp_Core_Common_ClassFindInfo;

    /**
     * path to the folder containing the plugins. defaults to AMFPHP_ROOTPATH . "/plugins/"
     * @var <String>
     */
    public $pluginsFolder;

    public function  __construct() {
        $this->serviceFolders = array();
        $this->serviceNames2Amfphp_Core_Common_ClassFindInfo = array();
        $this->pluginsFolder = AMFPHP_ROOTPATH . "/plugins/";
        
        $this->loadFromFile(null);
    }
    public function loadFromFile($path){
        //TODO
        array_push($this->serviceFolders, dirname(__FILE__) . "/services");
    }
}
?>
