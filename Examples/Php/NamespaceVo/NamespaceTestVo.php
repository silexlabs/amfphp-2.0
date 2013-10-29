<?php

/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Examples_ExampleService
 */

namespace NVo;
/**
 * dummy class for testing custom class conversion with namespace
 *
 * @package Amfphp_Examples_ExampleService
 * @author Ariel Sommeria-klein
 */
class NamespaceTestVo {
    public $dummyData = 'bla';
    
    /**
     * This must be set to be able to return the Vo, as without it the explicit type would be the fully qualified class name \NVo\NamespaceTestVo
     * @var string 
     */
    public $_explicitType = 'NamespaceTestVo';
    
}

?>
