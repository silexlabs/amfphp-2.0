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
        $hookManager = Amfphp_Core_HookManager::getInstance();

        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_SERIALIZED, array($this, "packetRequestSerializedHandler"));
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_DESERIALIZED, array($this, "packetRequestDeserializedHandler"));
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_RESPONSE_DESERIALIZED, array($this, "packetResponseDeserializedHandler"));
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_RESPONSE_SERIALIZED, array($this, "packetResponseSerializedHandler"));
    }

    public static function logMessage($message){
        $fh = fopen(self::LOG_FILE_PATH, 'a');
        if(!$fh){
            throw new Amfphp_Core_Exception("couldn't open log file for writing");
        }
        fwrite($fh, $message . "\n");
        fclose($fh);

    }

/*
 get this for test logging
    private function logMessage($message){
        $fh = fopen("bla.txt", 'a');
        if(!$fh){
            throw new Amfphp_Core_Exception("couldn't open log file for writing");
        }
        fwrite($fh, $message . "\n");
        fclose($fh);

    }
*/
    /**
     * logs the serialized incoming packet
     * @param <type> $rawData
     */
    public function packetRequestSerializedHandler($rawData){
        self::logMessage("serialized request packet : \n$rawData");
    }

    /**
     * logs the deserialized incoming packet.
     * @param packet $requestPacket
     * @return packet
     */
    public function packetRequestDeserializedHandler(Amfphp_Core_Amf_Packet $requestPacket){
        self::logMessage("deserialized request packet : \n" . print_r($requestPacket, true));
    }

    /**
     * logs the deserialized incoming packet.
     * @param packet $requestPacket
     * @return <array>
     */
    public function packetResponseDeserializedHandler(Amfphp_Core_Amf_Packet $responsePacket){
        self::logMessage("deserialized response packet : \n" . print_r($responsePacket, true));
    }

    /**
     * logs the deserialized incoming packet
     * @param <type> $rawData
     * @return <array>
     */
    public function packetResponseSerializedHandler($rawData){
        self::logMessage("serialized response packet : \n$rawData");
    }

    /**
     * logs the exception and the packet that caused it
     * @param packet $requestPacket
     * @return <array>
     */
    public function exceptionCaughtHandler(Exception $e, packet $requestPacket){
        self::logMessage("exception caught. exception :  \n " . $e->__toString() . "\nrequest : \n" . print_r($requestPacket, true));
    }

}
?>
