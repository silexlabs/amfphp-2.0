<?php

/**
 * utils for Amf handling
 *
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Util {
    /**
     * looks if the system is Big Endain or not
     * @return <Boolean>
     */
    static public function isSystemBigEndian(){
        $tmp = pack("d", 1); // determine the multi-byte ordering of this machine temporarily pack 1
        return ($tmp == "\0\0\0\0\0\0\360\77");
    }



    /**
     * there seems to be some confusion in the php doc as to where best to get the raw post data from.
     * try $GLOBALS['HTTP_RAW_POST_DATA'] and php://input
     *
     * @return <String> it's a binary stream, but there seems to be no better type than String for this.
     */
    static public function getRawPostData(){
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            return $GLOBALS['HTTP_RAW_POST_DATA'];
        }else{
            return file_get_contents('php://input');
        }

    }

    static public function d($obj){
        return $obj;
    }
    /**
     * applies a function to all objects contained by $obj and $obj itself.
     * iterates on $obj and its sub objects, which can iether be arrays or objects
     * @param mixed $obj the object/array that will be iterated on
     * @param array $callBack the function to apply to obj and subobjs. must take 1 parameter, and return the modified object
     * @param int $recursionDepth current recursion depth. The first call should be made with this set 0
     * @param int $maxRecursionDepth
     * @return mixed array or object, depending on type of $obj
     */
    static public function applyFunctionToContainedObjects($obj, $callBack, $recursionDepth, $maxRecursionDepth){
        if($recursionDepth == $maxRecursionDepth){
            throw new Amfphp_Core_Exception("couldn't recurse deeper on object");
        }
        //apply callBack to obj itself
        $obj = call_user_func($callBack, $obj);
        foreach($obj as $key => $data) { // loop over each element
            $modifiedData = null;
            if(is_object($data) || is_array($data)){
                //data is complex, so don't apply callback directly, but recurse on it
                $modifiedData = self::applyFunctionToContainedObjects($data, $callBack, $recursionDepth + 1, $maxRecursionDepth);
            }else{
                //data is simple, so apply data
                $modifiedData = call_user_func($callBack, $data);
            }
            //store converted data
            if(is_array($obj)){
                $obj[$key] = $modifiedData;
            }else{
                $obj->$key = $modifiedData;
            }

        }

        return $obj;
    }
}
?>
