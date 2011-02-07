<?php
/**
 *
 * place holder class for the variables necessary to make a service call
 *
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Common_ServiceCallParameters {


    /**
     * the name of the service. 
     * The service name can either be just the name of the class (MirrorService) or include a path(package/MirrorService)
     * separator for path can only be "/"
     *
     * @var String
     */
    public $serviceName;

    /**
     * the name of the method to execute on the service object
     * @var String
     */
    public $methodName;

    /**
     * the parameters to pass to the method being called on the service
     * @var <array>
     */
    public $methodParameters;

}
?>
