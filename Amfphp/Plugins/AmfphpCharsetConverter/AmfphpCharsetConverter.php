<?php
/**
 * Converts strings to the right encoding in incoming and outgoing packets
 * This works for nested objects.
 *
 * @author Ariel Sommeria-Klein
 */
class AmfphpCharsetConverter {
    const MAX_RECURSION_DEPTH = 10;

    /**
     * don't do anything
     */
    const METHOD_NONE = "none";

    /**
     * uses the iconv library for reencoding
     */
    const METHOD_ICONV = "iconv";

    /**
     * uses the mbstring library for reencoding
     */
    const METHOD_MBSTRING = "mbstring";

    /**
     * uses the recode library for reencoding
     */
    const METHOD_RECODE = "recode";
    
    /**
     *  uses the XML function utf8_decode and encode for reencoding - ISO-8859-1 only
     */
    const METHOD_UTF8_DECODE = "utf8_decode";
    
    /**
     * the reencoding method. One of the METHOD_XXX consts defined above.
     * @var String
     */
    public $method;

    /**
     * transliterate direction
     */
    const DIRECTION_PHP_TO_CLIENT = 0;

    /**
     * transliterate direction
     */
    const DIRECTION_CLIENT_TO_PHP = 1;

    /**
     * the Charset that is used in php default utf-8.
     * See all the possible codepages for iconv here:
     * http://www.gnu.org/software/libiconv/
     *
     * @var String
     */
    public $phpCharset;


    /**
     * the Charset that is used by the client. default utf-8
     * See all the possible codepages for iconv here:
     * http://www.gnu.org/software/libiconv/
     *
     * @var String
     */
    public $clientCharset;


    public function  __construct(array $config = null) {
        //defaults
        $this->clientCharset = "utf-8";
        $this->phpCharset = "utf-8";
        $this->method = self::METHOD_NONE;
        if($config){
            if(isset ($config["clientCharset"])){
                $this->clientCharset = $config["clientCharset"];
            }
            if(isset ($config["phpCharset"])){
                $this->phpCharset = $config["phpCharset"];
            }
            if(isset ($config["method"])){
                $this->method = $config["method"];
            }
        }

        //only add hooks if conversion is necessary
        if($this->method == self::METHOD_NONE){
            return;
        }
        if($this->clientCharset == $this->phpCharset){
            return;
        }
        $hookManager = Amfphp_Core_HookManager::getInstance();
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_REQUEST_DESERIALIZED, array($this, "packetRequestDeserializedHandler"));
        $hookManager->addHook(Amfphp_Core_Gateway::HOOK_RESPONSE_DESERIALIZED, array($this, "packetResponseDeserializedHandler"));
    }

    /**
     * convert the string. finds the proper encoding depending on direction
     * @param <type> $string data to convert
     * @param <type> $direction one of the DIRECTION_XXX consts described above
     * @return <type>
     */
    private function transliterate($string, $direction)
    {
        if($this->clientCharset == $this->phpCharset){
            return $string;
        }

        $fromCharset = null;
        $toCharset = null;
        if($direction == self::DIRECTION_CLIENT_TO_PHP){
            $fromCharset = $this->clientCharset;
            $toCharset = $this->phpCharset;
        }else{
            $fromCharset = $this->phpCharset;
            $toCharset = $this->clientCharset;
        }
        
        switch($this->method)
        {
            case self::METHOD_NONE :
                return $string;
            case self::METHOD_ICONV:
                return iconv($fromCharset, $toCharset, $string);
            case self::METHOD_UTF8_DECODE:
                return ($direction == self::DIRECTION_CLIENT_TO_PHP ? utf8_decode($string) : utf8_encode($string));
            case self::METHOD_MBSTRING:
                return mb_convert_encoding($string, $fromCharset, $toCharset);
            case self::METHOD_RECODE:
                return recode_string($fromCharset . ".." . $toCharset, $string);
            default:
                return $string;
        }
    }

    /**
     * converts the strings
     * note: This is not a recursive function. Rather the recursion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     * @param mixed $obj
     * @return mixed
     */
    public function convertStringFromClientToPhpCharsets($obj){
        if(!is_string($obj)){
            return $obj;
        }

        return $this->transliterate($obj, self::DIRECTION_CLIENT_TO_PHP);

     }

    /**
     * converts untyped objects to their typed counterparts. Loads the class if necessary
     * @param packet $requestPacket
     * @return packet
     */
    public function packetRequestDeserializedHandler(Amfphp_Core_Amf_Packet $requestPacket){
        $requestPacket = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($requestPacket, array($this, "convertStringFromClientToPhpCharsets"), 0, self::MAX_RECURSION_DEPTH);
        return array($requestPacket);

    }

    /**
     * note: This is not a recursive function. Rather the recusrion is handled by Amfphp_Core_Amf_Util::applyFunctionToContainedObjects.
     * must be public so that Amfphp_Core_Amf_Util::applyFunctionToContainedObjects can call it
     *
     * @param mixed $obj
     * @return mixed
     */
    public function convertStringFromPhpToClientCharsets($obj){
        if(!is_string($obj)){
            return $obj;
        }
        return $this->transliterate($obj, self::DIRECTION_PHP_TO_CLIENT);
    }

    /**
     * looks at the outgoing packet and sets the explicit type field so that the serializer sends it properly
     * @param packet $responsePacket
     * @return <array>
     */
    public function packetResponseDeserializedHandler(Amfphp_Core_Amf_Packet $responsePacket){
        $responsePacket = Amfphp_Core_Amf_Util::applyFunctionToContainedObjects($responsePacket, array($this, "convertStringFromPhpToClientCharsets"), 0, self::MAX_RECURSION_DEPTH);
        return array($responsePacket);

    }

}
?>
