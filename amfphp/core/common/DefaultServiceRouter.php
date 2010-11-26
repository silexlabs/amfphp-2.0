<?php

/**
 *  The Service Router class is responsible for executing the remote service method and returning it's value.
 * based on the old "Executive" of php 1.9
 *
 * @author Ariel Sommeria-klein
 */
class DefaultServiceRouter implements IServiceRouter{
    
    /**
     * a dictionary of service classes. The key is the name of the service, the value is the class find info.
     * for example: AmfphpDiscoveryService -> (plugins/serviceBrowser/AmfphpDiscoveryService.php, AmfphpDiscoveryService)
     * The forward slash is important, don't use '\'!
     * 
     * @var <array> 
     */
    private $serviceNames2ClassFindInfo;

    public function  __construct($serviceNames2ClassFindInfo) {
        $this->serviceNames2ClassFindInfo = $serviceNames2ClassFindInfo;
    }

    /**
     * loads and instanciates a service class matching $serviceName, then calls the function defined by $functionName using $parameters as parameters
     * @param <string> $serviceName
     * @param <string> $functionName
     * @param <array> $parameters
     * @return <mixed> the result of the function call
     */
    public function executeServiceCall($serviceName, $functionName, $parameters){
        $classFindInfo = $this->serviceNames2ClassFindInfo[$serviceName];
        require_once $classFindInfo->absolutePath;
        $serviceObject = new $classFindInfo->className();
        return call_user_func_array(array($serviceObject, $functionName), $parameters);

    }
}
?>
