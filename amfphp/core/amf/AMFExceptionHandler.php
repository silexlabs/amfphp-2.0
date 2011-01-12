<?php
/**
 * analyzes an exception and returns an AMFPacket
 * TODO. the code here is really rough. It needs some enhancement, and some checking 
 *
 * @author Ariel Sommeria-klein
 */
class AMFExceptionHandler {
    /**
     * 
     * @param <Exception> $exception the exception object to analyze
     * @return <mixed>
     */
    public function handle($exception){
        $amfPacket = new AMFPacket();
        $errorPacketMessage = new AMFMessage();
        $errorPacketMessage->data = $exception->__toString();
        $amfPacket->addMessage($errorPacketMessage);
        return $amfPacket;
    }
}
?>
