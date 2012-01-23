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
     * The forward slash is important, don't use "\'!     
     * @var <array> of ClassFindInfo
     */
    public $serviceNames2ClassFindInfo;
    
    /**
     * set to true if you want the service router to check if the number of arguments received by amfPHP matches with the method being called.
     * This should be set to false in production for performance reasons
     * @var Boolean
     */
    public $checkArgumentCount;

    /**
     * paths to the folder containing the plugins. defaults to AMFPHP_ROOTPATH . '/Plugins/'
     * @var array
     */
    public $pluginsFolders;

    /**
     * array containing untyped plugin configuration data. Add as needed. The advised format is the name of the plugin as key, and then
     * paramName/paramValue pairs as an array.
     * example: array('plugin' => array( 'paramName' =>'paramValue'))
     * The array( 'paramName' =>'paramValue') will be passed as is to the plugin at construction time.
     * 
     * @var array
     */
    public $pluginsConfig;
    
    /**
     * array containing configuration data that is shared between the plugins. The format is paramName/paramValue pairs as an array.
     * 
     * @var array
     */
    public $sharedConfig;
    
    /**
     * if true, there will be detailed information in the error messages, including confidential information like paths.
     * So it is advised to set to true for development purposes and to false in production.
     * Set in the shared config.
     * @var Boolean
     */
    const CONFIG_RETURN_ERROR_DETAILS = 'returnErrorDetails';
    

    /**
     * array of plugins that are available but should be disabled
     * @var array
     */
    public $disabledPlugins;

    public function  __construct() {
        $this->serviceFolderPaths = array();
        $this->serviceFolderPaths [] = dirname(__FILE__) . '/../Services/';
        $this->serviceNames2ClassFindInfo = array();
        $this->pluginsFolders = array(AMFPHP_ROOTPATH . 'Plugins/');
        $this->pluginsConfig = array();
        //useful when developping. set to false for production
        $this->checkArgumentCount = true;
        
        $this->sharedConfig = array();
        $this->sharedConfig[self::CONFIG_RETURN_ERROR_DETAILS] = true;
        
        $this->disabledPlugins = array();
        //disable logging and error handler by default
        $this->disabledPlugins[] = 'AmfphpLogger';
        $this->disabledPlugins[] = 'AmfphpErrorHandler';
        
    }
}
?>
