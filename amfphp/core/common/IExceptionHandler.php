<?php

/**
 * An exception handler is passed an exception and must translate that into something that is serializable. In the case of Amf, it must return an Amfphp_Core_Amf_Packet object
 * containing the relevant information
 *
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IExceptionHandler {
    /**
     * a stronger typing for the return type would be better in the future. In the meantime mixed is ok. For Amf, return an Amfphp_Core_Amf_Packet
     * @param <Exception> $exception the exception object to analyze
     * @return <mixed>
     */
    public function handle($exception);
}
?>
