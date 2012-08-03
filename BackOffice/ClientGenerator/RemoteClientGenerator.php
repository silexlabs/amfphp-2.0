<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators

  /**
 * 
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class Amfphp_BackOffice_ClientGenerator_RemoteClientGenerator {
    public $includeUrl;
    
    /**
     * override to provide a custom text in the Client Generator UI button for this generator.
     * @return String 
     */
    public function getUiCallText(){
        return get_class($this);
    }    
    
    /**
     * override to provide a custom url for a page containing info for this generator.
     * @return String 
     */
    public function getInfoUrl(){
        return "http://www.silexlabs.org/amfphp/documentation/client-generators/";
    }
    
    /**
     *override to provide a custom url for the iframe interfae of the generator
     * @return String 
     */
    public function getIframeUrl(){
        return '';
    }
    
    /**
     * path to proxy.html file. necessary for calling back from iframe to host page
     * @return String 
     */
    public function getProxyUrl(){
        return '';
        
    }
        
        
}

?>
