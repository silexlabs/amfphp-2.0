<?php

/**
 *Interface for any service router. A service router should be able to find a service and call functions on it
 * 
 * @author Ariel Sommeria-klein
 */
interface IServiceRouter {

    /**
     * loads and instanciates a service class matching $serviceName, then calls the function defined by $functionName using $parameters as parameters
     * @param <string> $serviceName
     * @param <string> $functionName
     * @param <array> $parameters
     * @return <mixed> the result of the function call
     */
    public function executeServiceCall($serviceName, $functionName, $parameters);
}
?>
