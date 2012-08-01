<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples
 */

/**
 * an example plugin as in the documentation. It takes a sourceUrl param in the configuration, and changes the deserialized request to 'bla'. Useless, but explanatory.
 * @author Ariel Sommeria-klein
 * @package Amfphp_Examples
 */
class TestPlugin
{
    /**
     * dummy parameter
     * @var String
     */
    public $sourceUrl;

    /**
     * constructor.
     * @param array $config optional key/value pairs in an associative array. Used to override default configuration values.
     */
    public function __construct(array $config = null)
    {
        //default
        $this->sourceUrl = '';
        if ($config) {
            if (isset($config['sourceUrl'])) {
                $this->sourceUrl = $config['sourceUrl'];
            }
        }

        $filterManager = Amfphp_Core_FilterManager::getInstance();
        $filterManager->addFilter(Amfphp_Core_Gateway::FILTER_DESERIALIZED_REQUEST, $this, 'filterDeserializedRequest');
    }

    /**
     * sets deserialized request to “bla”
     * @param  mixed $deserializedRequest
     * @return mixed
     */
    public function filterDeserializedRequest($deserializedRequest)
    {
        return 'bla';
    }

}
