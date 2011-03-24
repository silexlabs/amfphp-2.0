<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_CustomClassConverter
 */

/**
 * Converts data from incoming packets with explicit types to custom classes.
 * If the vclass is not found, the object is unmodified.
 * Sets the explicit type marker in the data of the outgoing packets.
 * If the explicit type marker is already set in an outgoing object, the value is left as is.
 * This works for nested objects.
 * This is to support services and plugins setting the explicit type themselves.
 *
 * @package Amfphp_Plugins_CustomClassConverter
 * @author Ariel Sommeria-Klein
 */
class AmfphpCustomClassConverter {

     /**
     * paths to folders containing custom classes(relative or absolute)
     * @var array of paths
     */
    public $customClassFolderPaths;

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {
        //default
        $this->customClassFolderPaths = array(Amfphp_ROOTPATH . "/Services/Vo/");
        if($config){
            if(isset($config["customClassFolderPaths"])){
                $this->customClassFolderPaths = $config["customClassFolderPaths"];
            }
        }
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, "filterDeserializedRequest");
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, "filterDeserializedResponse");
    }


    /**
     * converts untyped objects to their typed counterparts. Loads the class if necessary
     * @param mixed $deserializedRequest
     * @return mixed
     */
    public function filterDeserializedRequest($deserializedRequest){
        $deserializedRequest = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedRequest, array($this, "convertToTyped"));
        return $deserializedRequest;

    }
    
    /**
     * looks at the outgoing packet and sets the explicit type field so that the serializer sends it properly
     * @param mixed $deserializedResponse
     * @return mixed
     */
    public function filterDeserializedResponse($deserializedResponse){
        $deserializedResponse = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedResponse, array($this, "markExplicitType"));
        return $deserializedResponse;

    }

    /**
     * if the object contains an explicit type marker, this method attempts to convert it to its typed counterpart
     * if the typed class is already available, then simply creates a new instance of it. If not,
     * attempts to load the file from the available service folders.
     * If then the class is still not available, the object is not converted
     * note: This is not a recursive function. Rather the recusrion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     * @param mixed $obj
     * @return mixed
     */
    public function convertToTyped($obj){
        if(!is_object($obj)){
            return $obj;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
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
                foreach($obj as $key => $data) { // loop over each element to copy it into typed object
                    if($key != $explicitTypeField){
                        $typedObj->$key = $data;
                    }
                }
                return $typedObj;

            }
        }

        return $obj;

     }


    /**
     * sets the the explicit type marker on the object and its sub-objects. This is only done if it not already set, as in some cases
     * the service class might want to do this manually.
     * note: This is not a recursive function. Rather the recusrion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     * 
     * @param mixed $obj
     * @return mixed
     */
    public function markExplicitType($obj){
        if(!is_object($obj)){
            return $obj;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $className = get_class ($obj);
        if($className != "stdClass" && !isset($obj->$explicitTypeField)){
            $obj->$explicitTypeField = $className;
        }
        return $obj;
    }


}
?>
