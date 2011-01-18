<?php

/**
 * This class exports some internal (public) methods. This way, those methods
 * can be tested separately.
 */

class AMFDeserializerWrapper extends core_amf_Deserializer
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

    public function readAMF3Data(){
        return parent::readAMF3Data();
    }

    public function readAMF3String()    {
        return parent::readAMF3String();
    }

    public function readAMF3Array( )
    {
        return parent::readAMF3Array();
    }

    public function readAMF3Object()
    {
        return parent::readAMF3Object();
    }

    public function readAMF3ByteArray()
    {
        return parent::readAmf3ByteArray();
    }


}
?>