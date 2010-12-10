<?php

/**
 * AMFBody is a data type that encapsulates all of the various properties a body object can have.
 *
 * @author Ariel Sommeria-klein
 */
class AMFBody {
    /**
     * parsed to a service name and a function name. supported separators for the targetURI are "." and "/"
     * The service name can either be just the name of the class (MirrorService) or include a path(package/MirrorService)
     * example of full targetURI package/MirrorService/mirrorFunction
     *
     * @var <String>
     */
    public $targetURI = "";

    /**
     *
     * @var <String>
     */
    public $responseURI = "";

    /**
     *
     * @var <mixed>
     */
    public $data;

    /**
     * the name of the service. parsed from targetURI
     * The service name can either be just the name of the class (MirrorService) or include a path(package/MirrorService)
     * separator for path can only be "/"
     *
     * @var <String>
     */
    public $serviceName;

    /**
     * the nbame of the function to execute on the service object
     * @var <String>
     */
    public $functionName;
    
}
?>
