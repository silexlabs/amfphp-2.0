<?php

/**
 * responsable for loading and maintaining AMFPHP configuration
 *
 * @author Ariel Sommeria-klein
 */
class core_Config {

    /**
     * paths to folders containing services(relative or absolute)
     * @var <array> of paths
     */
    public $serviceFolderPaths;

    /**
     * a dictionary of service classes represented in a core_common_ClassFindInfo.
     * The key is the name of the service, the value is the class find info.
     * for example: AmfphpDiscoveryService -> new ClassfindInfo( ... /plugins/serviceBrowser/AmfphpDiscoveryService.php, AmfphpDiscoveryService)
     * The forward slash is important, don't use '\'!     
     * @var <array> of core_common_ClassFindInfo
     */
    public $serviceNames2ClassFindInfo;

    /**
     * path to the folder containing the plugins. defaults to AMFPHP_ROOTPATH . "/plugins/"
     * @var <String>
     */
    public $pluginsFolder;

    public function  __construct() {
        $this->serviceFolders = array();
        $this->serviceNames2ClassFindInfo = array();
        $this->pluginsFolder = AMFPHP_ROOTPATH . "/plugins/";
        
        $this->loadFromFile(null);
    }
    public function loadFromFile($path){
        //TODO
        array_push($this->serviceFolders, dirname(__FILE__) . "/services");
    }
}
?>
