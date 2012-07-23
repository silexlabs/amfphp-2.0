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
    protected function getServiceNames(array $serviceFolderPaths, array $serviceNames2ClassFindInfo) {
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
        $serviceNames = $this->getServiceNames(self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
        $ret = array();
        foreach ($serviceNames as $serviceName) {
            $serviceObject = Amfphp_Core_Common_ServiceRouter::getServiceObjectStatically($serviceName, self::$serviceFolderPaths, self::$serviceNames2ClassFindInfo);
            $objR = new ReflectionObject($serviceObject);
            
            $methodRs = $objR->getMethods(ReflectionMethod::IS_PUBLIC);
            $methods = array();
            foreach ($methodRs as $methodR) {
                $methodName = $methodR->name;
                
                if(substr($methodName, 0, 1) == '_'){
                    //methods starting with a '_' as they are reserved, so filter them out 
                    continue;
                }
                
                $parameters = array();
                $paramRs = $methodR->getParameters();
                
                foreach ($paramRs as $paramR) {
                    $parameterName = $paramR->name;
                    $type = '';
                    if($paramR->getClass()){
                        $type = $paramR->getClass()->name;
                    }
                    $parameterInfo = new AmfphpDiscovery_ParameterDescriptor($parameterName, $type);
                    $parameters[] = $parameterInfo;
                }
                $methodInfo = new AmfphpDiscovery_MethodDescriptor($methodName, $parameters);
                $methods[$methodName] = $methodInfo;
            }
            $serviceInfo = new AmfphpDiscovery_ServiceDescriptor($serviceName, $methods);
            $ret[$serviceName] = $serviceInfo;
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
