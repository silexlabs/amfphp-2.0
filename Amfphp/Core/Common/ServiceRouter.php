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
 * based on the old 'Executive' of php 1.9. It looks for a service either explicitely defined in a
 * ClassFindInfo object, or in a service folder.
 *
 * @package Amfphp_Core_Common
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ServiceRouter {
    /**
     * filter called when the service object is created. Useful for authentication
     * @param Object $serviceObject 
     * @param string $serviceName
     * @param string $methodName
     * @param array $parameters
     */
    const FILTER_SERVICE_OBJECT = 'FILTER_SERVICE_OBJECT';
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
     * check parameters. This is useful for development, but should be disabled for production
     * @var Boolean
     */
    public $checkArgumentCount;

    /**
     *
     * @param array $serviceFolderPaths folders containing service classes
     * @param array $serviceNames2ClassFindInfo a dictionary of service classes represented in a ClassFindInfo.
     * @param Boolean $checkArgumentCount
     */
    public function __construct($serviceFolderPaths, $serviceNames2ClassFindInfo, $checkArgumentCount = false) {
        $this->serviceFolderPaths = $serviceFolderPaths;
        $this->serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;
        $this->checkArgumentCount = $checkArgumentCount;
    }

    /**
     * get a service object by its name. Looks for a match in serviceNames2ClassFindInfo, then in the defined service folders.
     * If none found, an exception is thrown
     * @todo maybe option for a fully qualified class name. 
     * @param String $serviceName
     * @return serviceName
     */
    public function getServiceObject($serviceName) {
        $serviceObject = null;
        if (isset($this->serviceNames2ClassFindInfo[$serviceName])) {
            $classFindInfo = $this->serviceNames2ClassFindInfo[$serviceName];
            require_once $classFindInfo->absolutePath;
            $serviceObject = new $classFindInfo->className();
        } else {
            $serviceNameWithSlashes = str_replace('.', '/', $serviceName);
            $serviceIncludePath = $serviceNameWithSlashes . '.php';
            $exploded = explode('/', $serviceNameWithSlashes);
            $className = $exploded[count($exploded) - 1];
            //no class find info. try to look in the folders
            foreach ($this->serviceFolderPaths as $folderPath) {
                $servicePath = $folderPath . $serviceIncludePath;
                if (file_exists($servicePath)) {
                    require_once $servicePath;
                    $serviceObject = new $className();
                    break;
                }
            }
        }

        if (!$serviceObject) {
            throw new Amfphp_Core_Exception("$serviceName service not found ");
        }
        return $serviceObject;
    }

    /**
     * loads and instanciates a service class matching $serviceName, then calls the function defined by $methodName using $parameters as parameters
     * throws an exception if service not found.
     * if the service exists but not the function, an exception is thrown by call_user_func_array. It is pretty explicit, so no further code was added
     *
     * @param string $serviceName
     * @param string $methodName
     * @param array $parameters
     * @return mixed the result of the function call
     *
     */
    public function executeServiceCall($serviceName, $methodName, array $parameters) {
        $serviceObject = $this->getServiceObject($serviceName);
        $serviceObject = Amfphp_Core_FilterManager::getInstance()->callFilters(self::FILTER_SERVICE_OBJECT, $serviceObject, $serviceName, $methodName, $parameters);

        if (!method_exists($serviceObject, $methodName)) {
            throw new Amfphp_Core_Exception("method $methodName not found on $serviceName object ");
        }
        
        if(substr($methodName, 0, 1) == '_'){
            throw new Exception("The method $methodName starts with a '_', and is therefore not accessible");
        }
        if($this->checkArgumentCount){
            $this->checkNumberOfArguments($serviceObject, $serviceName, $methodName, $parameters);            
        }
        return call_user_func_array(array($serviceObject, $methodName), $parameters);
    }

    /**
     * checks if the argument count received by amfPHP matches the argument count of the called method. 
     * @param type $serviceObject
     * @param type $serviceName
     * @param type $methodName
     * @param array $parameters 
     */
    private function checkNumberOfArguments($serviceObject, $serviceName, $methodName, array $parameters) {
        $method = new ReflectionMethod($serviceObject, $methodName);
        $numberOfRequiredParameters = $method->getNumberOfRequiredParameters();
        $numberOfParameters = $method->getNumberOfParameters();
        $numberOfProvidedParameters = count($parameters);
        if ($numberOfProvidedParameters < $numberOfRequiredParameters || $numberOfProvidedParameters > $numberOfParameters) {
            throw new Amfphp_Core_Exception("Invalid number of parameters for method $methodName in service $serviceName : $numberOfRequiredParameters  required, $numberOfParameters total, $numberOfProvidedParameters provided");
        }
    }

}

?>