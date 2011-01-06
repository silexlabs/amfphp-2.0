<?php

/**
 * AMFBody is a data type that encapsulates all of the various properties a body object can have.
 *
 * @author Ariel Sommeria-klein
 */
class AMFBody {
    /**
     * inthe case of a request:
     * parsed to a service name and a function name. supported separators for the targetURI are "." and "/"
     * The service name can either be just the name of the class (MirrorService) or include a path(package/MirrorService)
     * example of full targetURI package/MirrorService/mirrorFunction
     *
     * in the case of a response:
     * the request responseUri + OK/KO
     * for example: /1/onResult or /1/onStatus
     *
     * @var <String>
     */
    public $targetURI = "";

    /**
     * in the case of a request:
     * operation name, for example /1
     *
     * in the case of a response:
     * undefined
     * 
     * @var <String>
     */
    public $responseURI = "";

    /**
     *
     * @var <mixed>
     */
    public $data;


    public function  __construct($targetURI = "", $responseURI = "", $data = "") {
        $this->targetURI = $targetURI;
        $this->responseURI = $responseURI;
        $this->data = $data;
    }
    
    
}
?>
