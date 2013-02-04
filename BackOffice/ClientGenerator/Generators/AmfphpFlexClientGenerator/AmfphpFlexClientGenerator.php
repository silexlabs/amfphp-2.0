<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Backoffice_Generators
 */

 /**
 * generates a Flash project for consumption of amfPHP services
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Backoffice_Generators
 */
class AmfphpFlexClientGenerator extends Amfphp_BackOffice_ClientGenerator_LocalClientGenerator {
    
    /**
     * constructor
     */
    public function __construct() {
        parent::__construct(array('as', 'mxml', 'xml'), dirname(__FILE__) . '/Template');
    }
        
    /**
     * get ui call text
     * @return string
     */
    public function getUiCallText() {
        return "Flex";
        
    }
    
    /**
     * info url
     * @return string
     */
    public function getInfoUrl(){
        return "http://www.silexlabs.org/amfphp/documentation/client-generators/flex/";
    }
}

?>
