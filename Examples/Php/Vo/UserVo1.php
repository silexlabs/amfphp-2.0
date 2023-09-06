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

/**
 * an example Value Object(Vo) object, used by VoService
 * @package Amfphp_Examples_ExampleService
 * @author Ariel Sommeria-klein
 */
#[AllowDynamicProperties]
class UserVo1 {
    /**
     *name
     * @var string 
     */
    public $name;
    
    /**
     *age
     * @var int 
     */
    public $age;
    
    /**
     * status
     * @var string 
     */
    public $status;
}

?>
