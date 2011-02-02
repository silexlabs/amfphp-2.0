<?php

/**
 * logs requests and responses in their serialized and deserialized forms. Note that this a crude logging system, with no levels, targets etc. like Log4j for example.
 * It is as such to be used for development purposes, but not for production
 *
 * @author Ariel Sommeria-klein
 */
class AmfphpLogger {


    const LOG_FILE_PATH = "amfphplog.log";

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        $hookManager = Amfphp_Core_HookManager::getInstance();

        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_SERIALIZED, $this, "requestSerializedHandler");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_DESERIALIZED, $this, "requestDeserializedHandler");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_RESPONSE_DESERIALIZED, $this, "responseDeserializedHandler");
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_RESPONSE_SERIALIZED, $this, "responseSerializedHandler");
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
     * @param String $rawData
     */
    public function requestSerializedHandler($rawData){
        self::logMessage("serialized request : \n$rawData");
    }

    /**
     * logs the deserialized request
     * @param mixed $deserializedRequest
     */
    public function requestDeserializedHandler($deserializedRequest){
        self::logMessage("deserialized request : \n" . print_r($deserializedRequest, true));
    }

    /**
     * logs the deserialized response
     * @param packet $deserializedResponse
     */
    public function responseDeserializedHandler($deserializedResponse){
        self::logMessage("deserialized response : \n" . print_r($deserializedResponse, true));
    }

    /**
     * logs the deserialized incoming packet
     * @param <type> $rawData
     */
    public function responseSerializedHandler($rawData){
        self::logMessage("serialized response : \n$rawData");
    }

    /**
     * logs the exception and the packet that caused it
     * @param packet $requestPacket
     */
    public function exceptionCaughtHandler(Exception $e, packet $requestPacket){
        self::logMessage("exception caught. exception :  \n " . $e->__toString() . "\nrequest : \n" . print_r($requestPacket, true));
    }

}
?>
