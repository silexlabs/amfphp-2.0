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
 * Contains all collected information about a service. This information will be used by the generator.
 *
 * @author Ariel Sommeria-klein
 * @package Amfphp_Plugins_Discovery
 */
class AmfphpDiscovery_ServiceDescriptor
{
     public $name;
    /**
     *
     * @var array of MethodInfo
     */
    public $methods;

    /**
     *
     * @var string class level comment
     */
    public $comment;

    /**
     *
     * @param string $name
     * @param array  $methods
     * @param string $comment
     * @param array  $tags
     */
    public function __construct($name, array $methods, $comment)
    {
        $this->name = $name;
        $this->methods = $methods;
        $this->comment = $comment;
    }
}
