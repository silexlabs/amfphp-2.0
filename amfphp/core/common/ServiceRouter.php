<?php

/**
 *  The Service Router class is responsible for executing the remote service method and returning it's value.
 * based on the old "Executive" of php 1.9. It looks for a service either explicitely defined in a
 * ClassFindInfo object, or in a service folder.
 *
 * @author Ariel Sommeria-klein
 */
class ServiceRouter implements IServiceRouter{
    /**
     * hook called when the service object is created. Useful for authentication
     * @param <String> the raw http data
     */
    const HOOK_SERVICE_OBJECT_CREATED = "HOOK_SERVICE_OBJECT_CREATED";
     /**
     * paths to folders containing services(relative or absolute)
     * @var <array> of paths
     */
    private $serviceFolderPaths;

    /**
     *
     * @var <array> of ClassFindInfo
     */
    private $serviceNames2ClassFindInfo;

    /**
     *
     * @param <array> $serviceFolderPaths folders containing service classes
     * @param <array> $serviceNames2ClassFindInfo a dictionary of service classes represented in a ClassFindInfo.
     */
    public function  __construct($serviceFolderPaths, $serviceNames2ClassFindInfo) {
        $this->serviceFolderPaths = $serviceFolderPaths;
        $this->serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;
    }

    /**
     * loads and instanciates a service class matching $serviceName, then calls the function defined by $methodName using $parameters as parameters
     * throws an exception if service not found.
     * if the service exists but not the function, an exception is thrown by call_user_func_array. It is pretty explicit, so no furher code was added
     *
     * @param <string> $serviceName
     * @param <string> $methodName
     * @param <array> $parameters
     * @return <mixed> the result of the function call
     *
     */
    public function executeServiceCall($serviceName, $methodName, $parameters){
        $serviceObject = null;
        if(isset ($this->serviceNames2ClassFindInfo[$serviceName])){
            $classFindInfo = $this->serviceNames2ClassFindInfo[$serviceName];
            require_once $classFindInfo->absolutePath;
            $serviceObject = new $classFindInfo->className();
        }else{
            //no class find info. try to look in the folders
            foreach($this->serviceFolderPaths as $folderPath){
                $servicePath = $folderPath . "/" . $serviceName . ".php";
                if(file_exists($servicePath)){
                    require_once $servicePath;
                    $serviceObject = new $serviceName();
                }
            }
            
        }

        if(!$serviceObject){
            throw new Exception("$serviceName service not found ");
        }
        if(!method_exists($serviceObject, $methodName)){
            throw new Exception("method  $methodName not found on $serviceName object ");
        }
        
        return call_user_func_array(array($serviceObject, $methodName), $parameters);

    }
}
?>
