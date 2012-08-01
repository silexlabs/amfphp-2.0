<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Discovery
 */

/**
*  includes
*  */
require_once dirname(__FILE__) . '/AmfphpDiscoveryService.php';
require_once dirname(__FILE__) . '/MethodDescriptor.php';
require_once dirname(__FILE__) . '/ParameterDescriptor.php';
require_once dirname(__FILE__) . '/ServiceDescriptor.php';

/**
 * adds the discovery service, a service that returns information about available services.
 * Don't use on a production server unless you know what you're doing!
 * @package Amfphp_Plugins_Discovery
 * @author Ariel Sommeria-Klein
 */
class AmfphpDiscovery
{
    /**
    * array of files and folders to ignore during introspection of the services dir
    * e.g. ignore dBug.php, an entire directory called 'classes' and also a subdirectory of one of the service directories (Vo/)
    * $this->pluginsConfig = array('AmfphpDiscovery' 	=> array('excludePaths' => array('dBug', 'classes', 'Vo/')));
     * default is for ignoring Vo/ folder
    */
    protected $excludePaths = array('Vo/');

    /**
     * constructor.
     * adds filters to grab config about services and add discovery service. Low priority so that other plugins can add their services first
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null)
    {
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this, 'filterServiceNames2ClassFindInfo', 99);
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_FOLDER_PATHS, $this, 'filterServiceFolderPaths', 99);

        if (isset($config['excludePaths'])) {
            $this->excludePaths = $config['excludePaths'];
        }
        AmfphpDiscoveryService::$excludePaths = $this->excludePaths;
    }

     /**
     * grabs serviceFolderPaths from config
     * @param array serviceFolderPaths array of absolute paths
     */
    public function filterServiceFolderPaths(array $serviceFolderPaths)
    {
        AmfphpDiscoveryService::$serviceFolderPaths = $serviceFolderPaths;

        return $serviceFolderPaths;
    }
     /**
     * grabs serviceNames2ClassFindInfo from config and add discovery service
     * @param array $serviceNames2ClassFindInfo associative array of key -> class find info
     */
    public function filterServiceNames2ClassFindInfo(array $serviceNames2ClassFindInfo)
    {
        $serviceNames2ClassFindInfo["AmfphpDiscoveryService"] = new Amfphp_Core_Common_ClassFindInfo( dirname(__FILE__) . '/AmfphpDiscoveryService.php', 'AmfphpDiscoveryService');
        AmfphpDiscoveryService::$serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;

        return $serviceNames2ClassFindInfo;
    }
}
