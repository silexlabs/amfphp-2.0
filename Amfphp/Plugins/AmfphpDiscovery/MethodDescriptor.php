<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Plugins_Discovery
 * 
 */

/**
 * Contains all collected information about a service method.
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_MethodDescriptor {
    public $name;
    /**
     * 
     * @var array of ParameterInfo
     */
    public $parameters; 
    
    /**
     * @todo return type. This would have to be done with commentary tag analysis, doesn't work for now.
     * @var String 
     */
    //public $returns;
    
    
    /**
     *
     * @param String $name
     * @param array of ParameterInfo $parameters
     */
    public function __construct($name, array $parameters) {
        $this->name = $name;
        $this->parameters = $parameters;
    }
    
}

?>
