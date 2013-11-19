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
 * note: Logging multiple times with the same name is not possible!
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
    
    /**
     * service and method name. If they are multiple calls in request, they are spearated with a ', '
     * @var string
     */
    protected $uri;

    /**
     * was there an exception during service call.
     * todo. unused.
     * @var boolean 
     */
    protected $isException;

    
    /**
     * last measured time, or start time
     * @var float 
     */
    protected static $lastMeasuredTime;
    
    /**
     * various times.  for example ['startD' => 12 , 'stopD' => 30 ] 
     * means start of deserialization at 12 ms after the request was received, 
     * and end of deserialization 30 ms after start of deserialization.
     * @var array
     */
    protected static $times;

    /**
     * restrict access to amfphp_admin, the role set when using the back office. default is true. 
     * @var boolean
     */
    protected $restrictAccess = true;
    

    /**
     * constructor.
     * manages log path. If file exists at log path, adds hooks for logging.
     * @param array $config 
     */
    public function __construct(array $config = null) {
        self::$lastMeasuredTime = round(microtime(true) * 1000);
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERVICE_NAMES_2_CLASS_FIND_INFO, $this, 'filterServiceNames2ClassFindInfo');
        if (isset($config['logPath'])) {
            $this->logPath = $config['logPath'];
        }else{
            $this->logPath = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'log.txt.php';
        }
        AmfphpMonitorService::$logPath = $this->logPath;
        if(isset($config['restrictAccess'])){
            $this->restrictAccess = $config['restrictAccess'];    
        }
        AmfphpMonitorService::$restrictAccess = $this->restrictAccess;
        
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_RESPONSE, $this, 'filterSerializedResponse');
            
    }
    
    /**
     * measures time since previous call (or start time time if this the first call) , and stores it in the times array
     * public and static so that services can call this too to add custom times.
     * updates lastMeasuredTime
     * @param string $name 
     */
    public static function addTime($name){
        $now = round(microtime(true) * 1000);
        $timeSinceLastMeasure = $now - self::$lastMeasuredTime;
        self::$times[$name] = $timeSinceLastMeasure;
        self::$lastMeasuredTime = $now;
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
     * logs the time for end of deserialization, as well as grabs the target uris(service + method)
     * as each request has its own format, the code here must handle all deserialized request structures. 
     * if case not handled just don't set target uris, as data can still be useful even without them.
     * @param mixed $deserializedRequest
     */
    public function filterDeserializedRequest($deserializedRequest) {
        self::addTime('Deserialization');
        
        //AMF
        if(is_a($deserializedRequest, 'Amfphp_Core_Amf_Packet')){
            //add multiple uris split with a ', '
            for($i = 0; $i < count($deserializedRequest->messages); $i++){
                if($i > 0){
                    $this->uri .= ', ';
                }
                $message = $deserializedRequest->messages[$i];
                $this->uri .= $message->targetUri;
            }
        }else if(isset ($deserializedRequest->serviceName)){
        //JSON
            $this->uri = $deserializedRequest->serviceName . '/' . $deserializedRequest->methodName;
        }else if(isset ($deserializedRequest['serviceName'])){
            //GET, included request
            $this->uri = $deserializedRequest['serviceName'] . '/' . $deserializedRequest['methodName'];
        }
        
        
    }

    /**
     * logs the time for start of serialization
     * @param packet $deserializedResponse
     */
    public function filterDeserializedResponse($deserializedResponse) {
        self::addTime('Service Call');

    }

    /**
     * logs the time for end of serialization
     * ignores calls to AmfphpMonitorService/getData
     * @param mixed $rawData
     */
    public function filterSerializedResponse($rawData) {
        if($this->uri == 'AmfphpMonitorService/getData'){
            return;
        }
        self::addTime('Serialization');
        $record = new stdClass();
        $record->uri = $this->uri;
        $record->times = self::$times;
        file_put_contents($this->logPath, "\n" . serialize($record), FILE_APPEND);
        
    }
    
    
}

?>
