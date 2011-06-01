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
 * AS3 XMLDocument type. 
 * @see Amfphp_Core_Amf_Types_Xml
 *
 * @package Amfphp_Core_Amf_Types
 * @author Ariel Sommeria-klein
 */

class Amfphp_Core_Amf_Types_XmlDocument
{
	public $data;

	public function Amfphp_Core_Amf_Types_XmlDocument($data)
	{
		$this->data = $data;
	}
}
?>
