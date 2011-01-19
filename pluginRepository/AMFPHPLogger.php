<?php

/**
 * logs requests and responses in their serialized and deserialized forms. Note that this a crude logging system, with no levels, targets etc. like Log4j for example.
 * It is as such to be used for development purposes, but not for production
 *
 * @author Ariel Sommeria-klein
 */
class AMFPHPLogger {


    const LOG_FILE_PATH = "amfphplog.log";

    public function  __construct() {
        $hookManager = HookManager::getInstance();

        $hookManager->addHook(Gateway::HOOK_PACKET_REQUEST_SERIALIZED, array($this, "packetRequestSerializedHandler"));
        $hookManager->addHook(Gateway::HOOK_PACKET_REQUEST_DESERIALIZED, array($this, "packetRequestDeserializedHandler"));
        $hookManager->addHook(Gateway::HOOK_PACKET_RESPONSE_DESERIALIZED, array($this, "packetResponseDeserializedHandler"));
        $hookManager->addHook(Gateway::HOOK_PACKET_RESPONSE_SERIALIZED, array($this, "packetResponseSerializedHandler"));
    }

    private function logMessage($message){
        $fh = fopen(self::LOG_FILE_PATH, 'a');
        if(!$fh){
            throw new AmfphpException("couldn't open log file for writing");
        }
        fwrite($fh, $message . "\n");
        fclose($fh);

    }

    /**
     * logs the serialized incoming packet
     * @param <type> $rawData
     */
    public function packetRequestSerializedHandler($rawData){
        $this->logMessage("serialized request packet : \n$rawData");
    }

    /**
     * logs the deserialized incoming packet.
     * @param packet $requestPacket
     * @return packet
     */
    public function packetRequestDeserializedHandler(AMFPacket $requestPacket){
        $this->logMessage("deserialized request packet : \n" . print_r($requestPacket, true));
    }

    /**
     * logs the deserialized incoming packet.
     * @param packet $requestPacket
     * @return <array>
     */
    public function packetResponseDeserializedHandler(AMFPacket $responsePacket){
        $this->logMessage("deserialized response packet : \n" . print_r($responsePacket, true));
    }

    /**
     * logs the deserialized incoming packet
     * @param <type> $rawData
     * @return <array>
     */
    public function packetResponseSerializedHandler($rawData){
        $this->logMessage("serialized response packet : \n$rawData");
    }

    /**
     * logs the exception and the packet that caused it
     * @param packet $requestPacket
     * @return <array>
     */
    public function exceptionCaughtHandler(Exception $e, packet $requestPacket){
        $this->logMessage("exception caught. exception :  \n " . $e->__toString() . "\nrequest : \n" . print_r($requestPacket, true));
    }

}
?>
