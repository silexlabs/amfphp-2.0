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
 * analyses existing services. Warning: if 2 or more services have the same name, t-only one will appear in the returned data, 
 * as it is an associative array using the service name as key. 
 * @package Amfphp_Plugins_Discovery
 * @author Ariel Sommeria-Klein
 */
class AmfphpDiscoveryService {
    /**
     * @see AmfphpDiscovery
     * @var array of strings(patterns)
     */
    public static $excludePaths;
    
    /**
     * paths to folders containing services(relative or absolute). set by plugin.
     * @var array of paths
     */
    public static $serviceFolderPaths;

    /**
     *
     * @var array of ClassFindInfo. set by plugin.
     */
    public static $serviceNames2ClassFindInfo;
    

        /**
* finds classes in folder. If in subfolders add the relative path to the name.
* recursive, so use with care.
* @param type $rootPath
* @param type $subFolder
* @return type
*/
    protected function searchFolderForServices($rootPath, $subFolder){
        $ret = array();
        $folderContent = scandir($rootPath . $subFolder);

        if ($folderContent) {
            foreach ($folderContent as $fileName) {
                //add all .php file names, but removing the .php suffix
                if (strpos($fileName, ".php")) {
                    $fullServiceName = $subFolder . substr($fileName, 0, strlen($fileName) - 4);
                    $ret[] = $fullServiceName;
             }else if((substr ($fileName, 0, 1) != '.') && is_dir($rootPath . $subFolder . $fileName)){
                    $ret = array_merge($ret, $this->searchFolderForServices($rootPath, $subFolder . $fileName . '/'));
                }
            }
        }
        return $ret;
        
    }

    /**
* returns a list of available services
* @return array of service names
*/
    protected function getAvailableServiceNames(array $serviceFolderPaths, array $serviceNames2ClassFindInfo) {
        $ret = array();
        foreach ($serviceFolderPaths as $serviceFolderPath) {
            $ret = array_merge($ret, $this->searchFolderForServices($serviceFolderPath, ''));
        }

        foreach ($serviceNames2ClassFindInfo as $key => $value) {
            $ret[] = $key;
        }
        
        return $ret;
    }
    
    /**
     * does the actual collection of data about available services
     * @return array of AmfphpDiscovery_ServiceInfo
     */
    public function discover(){
        $availableServiceNames = $this->getAvailableServiceNames(self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
        $ret = array();
        foreach ($availableServiceNames as $availableServiceName) {
            $serviceObject = Amfphp_Core_Common_ServiceRouter::getServiceObjectStatically($availableServiceName, self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
            $reflectionObj = new ReflectionObject($serviceObject);
            
            $availablePublicMethods = $reflectionObj->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array();
            foreach ($availablePublicMethods as $methodDescriptor) {
                $availableMethodName = $methodDescriptor->name;
                
                if(substr($availableMethodName, 0, 1) == '_'){
                    //methods starting with a '_' as they are reserved, so filter them out 
                    continue;
                }
                
                $parameters = array();
                $method = $reflectionObj->getMethod($availableMethodName);
                $parameterDescriptors = $method->getParameters();
                
                foreach ($parameterDescriptors as $parameterDescriptor) {
                    $availableParameterName = $parameterDescriptor->name;
                    $type = '';
                    if($parameterDescriptor->getClass()){
                        $type = $parameterDescriptor->getClass()->name;
                    }
                    $parameterInfo = new AmfphpDiscovery_ParameterDescriptor($availableParameterName, $type);
                    $parameters[] = $parameterInfo;
                }
                $methodInfo = new AmfphpDiscovery_MethodDescriptor($availableMethodName, $parameters);
                $methods[$availableMethodName] = $methodInfo;
            }
            $serviceInfo = new AmfphpDiscovery_ServiceDescriptor($availableServiceName, $methods);
            $ret[$availableServiceName] = $serviceInfo;
        }  
        //note : filtering must be done at the end, as for example excluding a Vo class needed by another creates issues
        foreach($ret as $serviceName => $serviceObj){
            foreach (self::$excludePaths as $excludePath){
                if (strpos($serviceName, $excludePath) !== false){
                    unset($ret[$serviceName]);
                    break;
                }
            }
        }
        return $ret;
    }
}

?>
