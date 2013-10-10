<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Monitor
 */
/**
 *  includes
 */
require_once dirname(__FILE__) . '/AmfphpMonitorService.php';

/**
 * logs monitoring information, and makes it possible to toggle logging and retrieve the data via a service.
 * The logging state is persisted by looking if the log file existes.
 * note that it is not possible with current system to stop logging but still keep the log file. 
 * It would be possible to address this by creating a separate file just to persist the logging state. 
 * @todo possibility to add custom times to logged data to measure duration of operations within the service call.
 * 
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Monitor
 */
class AmfphpMonitor {
    /**
     * path to log file. 
     * @var string 
     */
    protected $logPath;
    
    protected $startTime;
    
    /**
     * service and method names Usually only one, but in the case of AMF sometimes there can be multiple requests in one call.
     * @var array of strings 
     */
    protected $targetUris;

    /**
     * various times.  for example {'startD' => 12 } means start of deserialization at 12 ms after start.
     * @var array
     */
    protected $times;
    
    /**
     * was there an exception during service call.
     * @todo. unused.
     * @var boolean 
     */
    protected $isException;

    
    /**
     * constructor.
     * manages log path. If file exists at log path, adds hooks for logging.
     * @param array $config 
     */
    public function __construct(array $config = null) {
        $this->startTime = round(microtime(true) * 1000);
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this, 'filterServiceNames2ClassFindInfo');
        if (isset($config['logPath'])) {
            $this->logPath = $config['logPath'];
        }else{
            $this->logPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log.txt';
        }
        AmfphpMonitorService::$logPath = $this->logPath;
       // if(file_exists($this->logPath)){
            $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_REQUEST, $this, 'filterSerializedRequest');
            $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
            $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse');
            $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_RESPONSE, $this, 'filterSerializedResponse');
            
       // }
    }

    /**
     * add monitor service
     * @param array $serviceNames2ClassFindInfo associative array of key -> class find info
     */
    public function filterServiceNames2ClassFindInfo(array $serviceNames2ClassFindInfo) {
        $serviceNames2ClassFindInfo['AmfphpMonitorService'] = new Amfphp_Core_Common_ClassFindInfo(dirname(__FILE__) . '/AmfphpMonitorService.php', 'AmfphpMonitorService');
        return $serviceNames2ClassFindInfo;
    }
    
    /**
     * logs the serialized incoming packet
     * @param String $rawData
     */
    public function filterSerializedRequest($rawData) {
        $this->times['startD'] = round(microtime(true) * 1000) - $this->startTime;
        
    }

    /**
     * logs the deserialized request, as well as grabs the target uris(service + method)
     * as each request has its own format, the code here must handle all deserialized request structures. 
     * if case not handled just don't set target uris, as data can still be useful even without them.
     * @param mixed $deserializedRequest
     */
    public function filterDeserializedRequest($deserializedRequest) {
        
        //AMF
        if(is_a($deserializedRequest, 'Amfphp_Core_Amf_Packet')){
            foreach($deserializedRequest->messages as $message){
                $this->targetUris[] = $message->targetUri;
            }
        }else if(isset ($deserializedRequest->serviceName)){
        //JSON
            $this->targetUris[] = $deserializedRequest->serviceName . '/' . $deserializedRequest->methodName;
        }else if(isset ($deserializedRequest['serviceName'])){
            //GET, included request
            $this->targetUris[] = $deserializedRequest['serviceName'] . '/' . $deserializedRequest['methodName'];
        }
        
        $this->times['stopD'] = round(microtime(true) * 1000) - $this->startTime;
        
    }

    /**
     * logs the deserialized response
     * @param packet $deserializedResponse
     */
    public function filterDeserializedResponse($deserializedResponse) {
        $this->times['startS'] = round(microtime(true) * 1000) - $this->startTime;
    }

    /**
     * logs the deserialized incoming packet
     * note: ignores calls to AmfphpMonitorService
     * @param mixed $rawData
     */
    public function filterSerializedResponse($rawData) {

        $this->times['stopS'] = round(microtime(true) * 1000) - $this->startTime;
        $data = array('targetUris' => $this->targetUris, 'times' => $this->times);
        file_put_contents($this->logPath, serialize($data) . "\n", FILE_APPEND);
        
    }
    
    
}

?>
