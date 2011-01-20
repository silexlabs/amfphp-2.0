<?php
/**
 * Converts data from incoming packets with explicit types to custom classes.
 * Sets the explicit type marker in the data of the outgoing packets.
 * This works also for nested objects.
 * If the explicit type marker is already set in an outgoing object, the value is left as is.
 * This is to support services and plugins setting the explicit type themselves.
 *
 * @author Ariel Sommeria-Klein
 */
class CustomClassConverter {

     /**
     * paths to folders containing custom classes(relative or absolute)
     * @var array of paths
     */
    public $customClassFolderPaths;

    const MAX_RECURSION_DEPTH = 10;

    public function  __construct() {
        $this->customClassFolderPaths = array(AMFPHP_ROOTPATH . "/services/vo/");
        $hookManager = HookManager::getInstance();
        $hookManager->addHook(Gateway::HOOK_PACKET_REQUEST_DESERIALIZED, array($this, "packetRequestDeserializedHandler"));
        $hookManager->addHook(Gateway::HOOK_PACKET_RESPONSE_DESERIALIZED, array($this, "packetResponseDeserializedHandler"));
    }

    /**
     * if the object or any of its sub-objects contain an explicit type marker, this method attempts to convert it to its typed counterpart
     * if the typed class is already available, then simply creates a new instance of it. If not,
     * attempts to load the file from the available service folders.
     * If then the class is still not available, the object is not converted
     * This is a recursive function.
     * note: can't find a proper syntax to handle both objects and arrays, so some of the code is duplicated. If anyone has an idea give me a shout! A.S.
     * @param $obj it's either an object or an array
     * @param int $recursionDepth This is a counter for how deep the recursion is in the object that is being converted. If it is more than MAX_RECURSION_DEPTH, an exception is thrown.
     * This is to avoid looped references.
     * @return Object
     */
    private function convertToTyped($obj, $recursionDepth){
        if($recursionDepth >= self::MAX_RECURSION_DEPTH){
            throw new AmfphpException("can't convert object, it probably contains a looped reference");
        }

        $explicitTypeField = AMFConstants::FIELD_EXPLICIT_TYPE;
        $typedObj = null;
        if(isset($obj->$explicitTypeField)){
            $customClassName = $obj->$explicitTypeField;
            if(!class_exists($customClassName)){
                foreach($this->customClassFolderPaths as $folderPath){
                    $customClassPath = $folderPath . "/" . $customClassName . ".php";
                    if(file_exists($customClassPath)){
                        require_once $customClassPath;
                        break;
                    }
                }
            }
            if(class_exists($customClassName)){
                //class is available. Use it!
                $typedObj =  new $customClassName();
                //get rid of explicit type marker
                unset ($obj->$explicitTypeField);
            }else{
                //don't do anything, because the class wasn't found. 
            }
        }

        foreach($obj as $key => $data) { // loop over each element

            if(is_object($data) || is_array($data)){
                $convertedData = $this->convertToTyped($data, $recursionDepth + 1);
                if(is_array($obj)){
                    $obj[$key] = $convertedData;
                }else{
                    $obj->$key = $convertedData;
                }

            }
            if($typedObj){
                $typedObj->$key = $obj->$key;
            }
        }
        if($typedObj){
            return $typedObj;
        }else{
            return $obj;
        }
     }

    /**
     * converts untyped objects to their typed counterparts. Loads the class if necessary
     * @param packet $requestPacket
     * @return packet
     */
    public function packetRequestDeserializedHandler(AMFPacket $requestPacket){
        $numHeaders = count($requestPacket->headers);
        for($i = 0; $i < $numHeaders; $i++){
            $requestPacket->headers[$i]->value  = $this->convertToTyped($requestPacket->headers[$i]->value, 0);
        }
        $numMessages = count($requestPacket->messages);
        for($i = 0; $i < $numMessages; $i++){
            $requestPacket->messages[$i]->data  = $this->convertToTyped($requestPacket->messages[$i]->data, 0);
        }
        return array($requestPacket);

    }

    /**
     * sets the the explicit type marker on the object and its sub-objects. This is only done if it not already set, as in some cases
     * the service class might want to do this manually.
     * 
     * @param stdClass $obj
     * @param int $recursionDepth This is a counter for how deep the recursion is in the object that is being converted. If it is more than MAX_RECURSION_DEPTH, an exception is thrown.
     * This is to avoid looped references.
     * @return stdClass
     */
    private function markExplicitType($obj, $recursionDepth){
        if($recursionDepth >= self::MAX_RECURSION_DEPTH){
            throw new AmfphpException("can't markExplicitType on object, it probably contains a looped reference");
        }

        $explicitTypeField = AMFConstants::FIELD_EXPLICIT_TYPE;
        $className = get_class ($obj);
        if($className != "stdClass" && !isset($obj->$explicitTypeField)){
            $obj->$explicitTypeField = $className;
        }

        foreach($obj as $key => $data) { // loop over each element
            if(is_object($obj->$key)){
                $obj->$key = $this->markExplicitType($obj->$key, $recursionDepth + 1);
            }
        }
        return $obj;
    }

    /**
     * looks at the outgoing packet and sets the explicit type field so that the serializer sends it properly
     * @param packet $responsePacket
     * @return <array>
     */
    public function packetResponseDeserializedHandler(AMFPacket $responsePacket){
        $numHeaders = count($responsePacket->headers);
        for($i = 0; $i < $numHeaders; $i++){
            $responsePacket->headers[$i]->value  = $this->markExplicitType($responsePacket->headers[$i]->value, 0);
        }
        $numMessages = count($responsePacket->messages);
        for($i = 0; $i < $numMessages; $i++){
            $responsePacket->messages[$i]->data  = $this->markExplicitType($responsePacket->messages[$i]->data, 0);
        }
        return array($responsePacket);

    }

}
?>
