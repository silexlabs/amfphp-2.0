<?php
/**
 *  This file is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 * @package Amfphp_Core_Amf
 */

/**
 * Amf Undefined will be converted to and from this class
 * @package Amfphp_Core_Amf_Types
 * @author Ariel Sommeria-klein
 */
class Amfphp_Core_Amf_Types_Undefined
{
    public function exists()
    {
        return false;
    }

    public function __toString()
    {
        return 'undefined';
    }
}
