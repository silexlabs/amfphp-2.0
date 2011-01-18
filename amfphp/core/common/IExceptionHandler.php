<?php

/**
 * An exception handler is passed an exception and must translate that into something that is serializable. In the case of AMF, it must return an core_amf_Packet object
 * containing the relevant information
 *
 * @author Ariel Sommeria-klein
 */
interface core_common_IExceptionHandler {
    /**
     * a stronger typing for the return type would be better in the future. In the meantime mixed is ok. For AMF, return an core_amf_Packet
     * @param <Exception> $exception the exception object to analyze
     * @return <mixed>
     */
    public function handle($exception);
}
?>
