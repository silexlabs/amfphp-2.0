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
        $hookManager = core_HookManager::getInstance();

        $hookManager->addHook(core_Gateway::HOOK_PACKET_REQUEST_SERIALIZED, array($this, "amfPacketRequestSerializedHandler"));
        $hookManager->addHook(core_Gateway::HOOK_PACKET_REQUEST_DESERIALIZED, array($this, "amfPacketRequestDeserializedHandler"));
        $hookManager->addHook(core_Gateway::HOOK_PACKET_RESPONSE_DESERIALIZED, array($this, "amfPacketResponseDeserializedHandler"));
        $hookManager->addHook(core_Gateway::HOOK_PACKET_RESPONSE_SERIALIZED, array($this, "amfPacketResponseSerializedHandler"));
    }

    private function logMessage($message){
        $fh = fopen(self::LOG_FILE_PATH, 'a');
        if(!$fh){
            throw new Exception("couldn't open log file for writing");
        }
        fwrite($fh, $message . "\n");
        fclose($fh);

    }

    /**
     * logs the serialized incoming packet
     * @param <type> $rawData
     */
    public function amfPacketRequestSerializedHandler($rawData){
        $this->logMessage("serialized request packet : \n$rawData");
    }

    /**
     * logs the deserialized incoming packet.
     * @param core_amf_Packet $requestPacket
     * @return core_amf_Packet
     */
    public function amfPacketRequestDeserializedHandler(core_amf_Packet $requestPacket){
        $this->logMessage("deserialized request packet : \n" . print_r($requestPacket, true));
    }

    /**
     * logs the deserialized incoming packet.
     * @param core_amf_Packet $requestPacket
     * @return <array>
     */
    public function amfPacketResponseDeserializedHandler(core_amf_Packet $responsePacket){
        $this->logMessage("deserialized response packet : \n" . print_r($responsePacket, true));
    }

    /**
     * logs the deserialized incoming packet
     * @param <type> $rawData
     * @return <array>
     */
    public function amfPacketResponseSerializedHandler($rawData){
        $this->logMessage("serialized response packet : \n$rawData");
    }

    /**
     * logs the exception and the packet that caused it
     * @param core_amf_Packet $requestPacket
     * @return <array>
     */
    public function exceptionCaughtHandler(Exception $e, core_amf_Packet $requestPacket){
        $this->logMessage("exception caught. exception :  \n " . $e->__toString() . "\nrequest : \n" . print_r($requestPacket, true));
    }

}
?>
