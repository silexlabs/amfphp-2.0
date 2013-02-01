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
 * Contains all collected information about a service method parameter
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_ParameterDescriptor {

    /**
     * name
     * @var string
     */
    public $name;

    /**
     * This can be gathered in 2 manners: commentary tag analysis and type hinting analysis. For starters only the second method is used
     * @var String
     */
    public $type;

    /**
     * @todo 
     * @var Boolean
     */
    //public $isOptional;

    /**
     * constructor
     * @param String $name
     * @param String $type
     */
    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }

}

?>
