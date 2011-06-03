<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Core_Common
 */


/**
* The Service Router class is responsible for executing the remote service method and returning it's value.
* based on the old "Executive" of php 1.9. It looks for a service either explicitely defined in a
* ClassFindInfo object, or in a service folder.
*
* @package Amfphp_Core_Common
* @author Ariel Sommeria-klein
*/
class Amfphp_Core_Common_ServiceRouter{
    /**
* filter called when the service object is created. Useful for authentication
* @param String the raw http data
*/
    const FILTER_SERVICE_OBJECT = "FILTER_SERVICE_OBJECT";
     /**
* paths to folders containing services(relative or absolute)
* @var array of paths
*/
    public $serviceFolderPaths;

/**
*
* @var array of ClassFindInfo
*/
    public $serviceNames2ClassFindInfo;

    /**
    *
    * @param array $serviceFolderPaths folders containing service classes
    * @param array $serviceNames2ClassFindInfo a dictionary of service classes represented in a ClassFindInfo.
    */
    public function __construct($serviceFolderPaths, $serviceNames2ClassFindInfo) {
        $this->serviceFolderPaths = $serviceFolderPaths;
        $this->serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;
    }

    /**
     * get a service object by its name. Looks for a match in serviceNames2ClassFindInfo, then in the defined service folders.
     * If none found, an exception is thrown
     * @param String $serviceName
     * @return serviceName
     */
    public function getServiceObject($serviceName){
        $serviceObject = null;
        if(isset ($this->serviceNames2ClassFindInfo[$serviceName])){
            $classFindInfo = $this->serviceNames2ClassFindInfo[$serviceName];
            require_once $classFindInfo->absolutePath;
            $serviceObject = new $classFindInfo->className();
        }else{
            //no class find info. try to look in the folders
            foreach($this->serviceFolderPaths as $folderPath){
                $servicePath = $folderPath . $serviceName . ".php";
                if(file_exists($servicePath)){
                    require_once $servicePath;
                    $serviceObject = new $serviceName();
                }
            }

        }

        if(!$serviceObject){
            throw new Amfphp_Core_Exception("$serviceName service not found ");
        }
        return $serviceObject;
    }

    /**
    * loads and instanciates a service class matching $serviceName, then calls the function defined by $methodName using $parameters as parameters
    * throws an exception if service not found.
    * if the service exists but not the function, an exception is thrown by call_user_func_array. It is pretty explicit, so no furher code was added
    *
    * @param string $serviceName
    * @param string $methodName
    * @param array $parameters
    * @return mixed the result of the function call
    *
    */
    public function executeServiceCall($serviceName, $methodName, array $parameters){
        $serviceObject = $this->getServiceObject($serviceName);
        $serviceObject = Amfphp_Core_FilterManager::getInstance()->callFilters(self::FILTER_SERVICE_OBJECT, $serviceObject, $serviceName, $methodName);

        if(!method_exists($serviceObject, $methodName)){
            throw new Amfphp_Core_Exception("method $methodName not found on $serviceName object ");
        }

        return call_user_func_array(array($serviceObject, $methodName), $parameters);

    }
}
?>