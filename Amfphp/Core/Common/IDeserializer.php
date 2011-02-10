<?php
/**
 *  This file part is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * interface for deserializers. 
 * @author Ariel Sommeria-klein
 */
interface Amfphp_Core_Common_IDeserializer {
    
    /**
     * deserialize the data.
     * @param String $rawPostData
     * @param array $getData typically the $_GET array. 
     * @return mixed the deserialized data. For example an Amf packet.
     */
    public function deserialize($rawPostData, array $getData);
}
?>
