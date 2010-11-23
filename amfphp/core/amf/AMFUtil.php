<?php

/**
 * utils for AMF handling
 *
 * @author Ariel Sommeria-klein
 */
class AMFUtil {

    /**
     * looks if the system is Big Endain or not
     * @return <Boolean>
     */
    static public function isSystemBigEndian(){
        $tmp = pack("d", 1); // determine the multi-byte ordering of this machine temporarily pack 1
        return ($tmp == "\0\0\0\0\0\0\360\77");
    }
}
?>
