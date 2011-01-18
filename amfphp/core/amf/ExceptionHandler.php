<?php
/**
 * analyzes an exception and returns an core_amf_Packet
 * TODO. the code here is really rough. It needs some enhancement, and some checking 
 *
 * @author Ariel Sommeria-klein
 */
class core_amf_ExceptionHandler {
    /**
     * 
     * @param <Exception> $exception the exception object to analyze
     * @return <mixed>
     */
    public function handle($exception){
        $amfPacket = new core_amf_Packet();
        $errorPacketMessage = new core_amf_Message();
        $errorPacketMessage->data = $exception->__toString();
        $amfPacket->addMessage($errorPacketMessage);
        return $amfPacket;
    }
}
?>
