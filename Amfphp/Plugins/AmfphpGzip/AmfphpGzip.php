<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Gzip
 */


/**
 * Compress responses in gzip.
 *
 * Note that this a crude logging system, with no levels, targets etc. like Log4j for example.
 * It is as such to be used for development purposes, but not for production
 *
 * @package Amfphp_Plugins_Gzip
 * @author Alejandro Tabares
 */
class AmfphpGzip {

    const CONTENT_TYPE = Amfphp_Core_Amf_Constants::CONTENT_TYPE;

    protected $compression_level = 9;
    protected $compressed_length = 0;

    protected $content_encoding  = 'gzip';
    protected $vary              = 'Accept-Encoding';

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function  __construct(array $config = null) {

        if(!$this->is_compression_enable()) {
            return;
        }

        if(isset($config['level']) && $config['level']){
            $this->compression_level = $config['level'];
        }

        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_SERIALIZED_RESPONSE, $this, 'filterSerializedResponse');
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_HEADERS, $this, 'filterHeaders');
    }

    /**
     * Compress the serialized output
     * @param <type> $rawData
     */
    public function filterSerializedResponse($rawData){

        $data = gzencode($rawData, $this->compression_level);
        $this->compressed_length = $this->strlen($data);

        return $data;
    }

    /**
     * Sets appropriate headers for gzip enconded response
     * @param array $headers
     * @return array
     */
    public function filterHeaders($headers, $contentType){
        if ($contentType == self::CONTENT_TYPE && $this->compressed_length) {

            $headers['Content-length']   = $this->compressed_length;
            $headers['Content-Encoding'] = $this->content_encoding;
            $headers['Vary']             = $this->vary;
            return $headers;
        }
    }

    protected function is_compression_enable(){
        return function_exists('gzencode');
    }

    /**
     * Counts the number of characters in a UTF-8 string.
     * @param  string    The string to run the operation on.
     * @return int       The length of the string.
     */
    protected function strlen($text){

        $length = 0;

        if (function_exists('mb_strlen')) {
            $length = mb_strlen($text);
        }
        else {
            // Do not count UTF-8 continuation bytes.
            $length = strlen(preg_replace("/[\x80-\xBF]/", '', $text));
        }

        return $length;
    }

}
