<?php

/**
 * An exception handler is passed an exception and must translate that into something that is serializable. In the case of Amf, it must return an Amfphp_Core_Amf_Packet object
 * containing the relevant information
 *
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IExceptionHandler {
    /**
     * generates an object describing the exception.
     * @param Exception $exception the exception object to analyze
     * @return mixed an object describing the error, that will be serialized and sent back to the client
     */
    public function handleException(Exception $exception);
}
?>
