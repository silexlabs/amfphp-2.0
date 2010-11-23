<?php

/**
 * This class exports some internal (public) methods. This way, those methods
 * can be tested separately.
 */

class AMFSerializerWrapper extends AMFSerializer
{
    public function writeByte($b){
        parent::writeByte($b);
    }

    public function writeInt($n) {
        parent::writeInt($n);
    }

    public function writeLong($l) {
        parent::writeLong($l);
    }

    public function writeUtf($s) {
        parent::writeUtf($s);
    }
    public function writeDouble($s) {
        parent::writeDouble($s);
    }

    function writeBinary($s) {
        parent::writeBinary($a);
    }

    public function writeLongUtf($s) {
        parent::writeLongUtf($s);
    }

    public function writeNumber($d) {
        parent::writeNumber($d);
    }

    public function writeBoolean($d) {
        parent::writeBoolean($d);
    }

    public function writeString($d) {
        parent::writeString($d);
    }

    public function writeXML($d) {
        parent::writeXML($d);
    }

    public function writeDate($d) {
        parent::writeDate($d);
    }

    public function writeNull() {
        parent::writeNull();
    }

    public function writeUndefined() {
        parent::writeUndefined();
    }

    public function writeObjectEnd() {
        parent::writeObjectEnd();
    }

    public function writeArray($d) {
        parent::writeArray($d);
    }

    public function writeReference($d) {
        parent::writeReference($d);
    }

    public function writeTypedObject($d, $className) {
        parent::writeTypedObject($d, $className);
    }

}
?>