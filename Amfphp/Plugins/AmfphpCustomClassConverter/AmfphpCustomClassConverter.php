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
 * Converts data from incoming packets with explicit types to custom classes, and vice versa for the outgoing packets.
 * 
 * This plugin can be deactivated if the project doesn't use custom classes.
 * 
 * The AMF deserializer reads a typed AMF object as a stdObj class, and sets the AMF type to a reserved "explicit type" field.
 * This plugin will look at deserialized data and try to convert any such objects to a real custom class.
 * 
 * It works in the opposite way on the way out: The AMF serializer needs a stdObj class with the explicit type marker set 
 * to write a typed AMF object. This plugin will convert any typed PHP objects to a stdObj with the explicit type marker set.
 * 
 * The explicit type marker is defined in Amfphp_Core_Amf_Constants
 * 
 * If after deserialization the custom class is not found, the object is unmodified and the explicit type marker is left set.
 * If the explicit type marker is already set in an outgoing object, the value is left as is.
 * 
 * 
 * This works for nested objects.
 * 
 * 
 * If you don't need strong typing in PHP but would like the objects in your client to be strongly typed, you can:
 * For example a stdObj like this will be returned in AMF as MyVO
 * <code>
 * $returnObj = new stdObj();
 * $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
 * $returnObj->$explicitTypeField = "MyVO"; 
 * </code>
 * 
 * If you are using Flash, remember that you need to register the class alias so that Flash converts the MyVO AMF object to a Flash MyVO object.
 * If you are using Flex you can do this with the RemoteClass metadata tag.
 *  
 * @see Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE
 * @link http://help.adobe.com/en_US/FlashPlatform/reference/actionscript/3/flash/net/package.html#registerClassAlias%28%29 
 * @link http://livedocs.adobe.com/flex/3/html/metadata_3.html#198729
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
    public function __construct(array $config = null) {
        //default
        $this->customClassFolderPaths = array(AMFPHP_ROOTPATH . '/Services/Vo/');
        if ($config) {
            if (isset($config['customClassFolderPaths'])) {
                $this->customClassFolderPaths = $config['customClassFolderPaths'];
            }
        }
        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_RESPONSE, $this, 'filterDeserializedResponse');
    }

    /**
     * converts untyped objects to their typed counterparts. Loads the class if necessary
     * @param mixed $deserializedRequest
     * @return mixed
     */
    public function filterDeserializedRequest($deserializedRequest) {
        $deserializedRequest = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedRequest, array($this, 'convertToTyped'));
        return $deserializedRequest;
    }

    /**
     * looks at the outgoing packet and sets the explicit type field so that the serializer sends it properly
     * @param mixed $deserializedResponse
     * @return mixed
     */
    public function filterDeserializedResponse($deserializedResponse) {
        $deserializedResponse = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($deserializedResponse, array($this, 'markExplicitType'));
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
    public function convertToTyped($obj) {
        if (!is_object($obj)) {
            return $obj;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        if (isset($obj->$explicitTypeField)) {
            $customClassName = $obj->$explicitTypeField;
            if (!class_exists($customClassName)) {
                foreach ($this->customClassFolderPaths as $folderPath) {
                    $customClassPath = $folderPath . '/' . $customClassName . '.php';
                    if (file_exists($customClassPath)) {
                        require_once $customClassPath;
                        break;
                    }
                }
            }
            if (class_exists($customClassName)) {
                //class is available. Use it!
                $typedObj = new $customClassName();
                foreach ($obj as $key => $data) { // loop over each element to copy it into typed object
                    if ($key != $explicitTypeField) {
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
    public function markExplicitType($obj) {
        if (!is_object($obj)) {
            return $obj;
        }
        $explicitTypeField = Amfphp_Core_Amf_Constants::FIELD_EXPLICIT_TYPE;
        $className = get_class($obj);
        if ($className != 'stdClass' && !isset($obj->$explicitTypeField)) {
            $obj->$explicitTypeField = $className;
        }
        return $obj;
    }

}

?>
