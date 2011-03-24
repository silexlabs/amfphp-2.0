<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Tests_Amfphp_Core_Amf
 */


/**
 * This class exports some internal (public) methods. This way, those methods
 * can be tested separately.
 * @package Tests_Amfphp_Core_Amf
 * @author Ariel Sommeria-klein
 */

class AmfDeserializerWrapper extends Amfphp_Core_Amf_Deserializer
{
    public function readByte(){
        return parent::readByte();
    }

    public function readInt() {
        return parent::readInt();
    }

    public function readLong() {
        return parent::readLong();
    }

    public function readUtf() {
        return parent::readUtf();
    }
    public function readDouble() {
        return parent::readDouble();
    }

    public function readLongUtf() {
        return parent::readLongUtf();
    }

    public function readDate() {
        return parent::readDate();
    }

    public function readArray() {
        return parent::readArray();
    }

    public function  readObject() {
        return parent::readObject();
    }

    public function   readMixedArray() {
        return parent::readMixedArray();
    }

    public function readReference() {
        return parent::readReference();
    }

    public function readAmf3Data(){
        return parent::readAmf3Data();
    }

    public function readAmf3String()    {
        return parent::readAmf3String();
    }

    public function readAmf3Array( )
    {
        return parent::readAmf3Array();
    }

    public function readAmf3Object()
    {
        return parent::readAmf3Object();
    }

    public function readAmf3ByteArray()
    {
        return parent::readAmf3ByteArray();
    }


}
?>