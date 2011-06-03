<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Core
 */


/**
 * responsable for loading and maintaining Amfphp configuration
 *
 * @package Amfphp_Core
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Config {

    /**
     * paths to folders containing services(relative or absolute)
     * @var <array> of paths
     */
    public $serviceFolderPaths;

    /**
     * a dictionary of service classes represented in a ClassFindInfo.
     * The key is the name of the service, the value is the class find info.
     * for example: AmfphpDiscoveryService -> new ClassfindInfo( ... /Plugins/serviceBrowser/AmfphpDiscoveryService.php, AmfphpDiscoveryService)
     * The forward slash is important, don't use '\'!     
     * @var <array> of ClassFindInfo
     */
    public $serviceNames2ClassFindInfo;

    /**
     * path to the folder containing the plugins. defaults to Amfphp_ROOTPATH . "/Plugins/"
     * @var String
     */
    public $pluginsFolder;

    /**
     * array containing untyped plugin configuration data. Add as needed. The advised format is the name of the plugin as key, and then
     * paramName/paramValue pairs as an array.
     * example: array("plugin" => array( "paramName" =>"paramValue"))
     * The array( "paramName" =>"paramValue") will be passed as is to the plugin at construction time.
     * 
     * @var array
     */
    public $pluginsConfig;

    /**
     * array of plugins that are available but should be disabled
     * @var array
     */
    public $disabledPlugins;

    public function  __construct() {
        $this->serviceFolderPaths = array();
        $this->serviceFolderPaths [] = dirname(__FILE__) . "/../Services/";
        $this->serviceNames2ClassFindInfo = array();
        $this->pluginsFolder = Amfphp_ROOTPATH . "/Plugins/";
        $this->pluginsConfig = array();
        $this->disabledPlugins = array();
        //disable logging by default
        $this->disabledPlugins[] = "AmfphpLogger";
        //this is a bit experimental and only really useful when getting badly formed responses through errors. so disabled by default
        $this->disabledPlugins[] = "AmfphpErrorHandler";
        
    }
}
?>
