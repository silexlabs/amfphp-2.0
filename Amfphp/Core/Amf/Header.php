<?php
/**
 *  This file part is part of amfPHP
 *
 * LICENSE
 *
 * This source file is subject to the license that is bundled
 * with this package in the file license.txt.
 */

/**
 * Amfphp_Core_Amf_Header is a data type that represents a single header passed via Amf
 *
 * Amfphp_Core_Amf_Header encapsulates the different Amf keys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 Amfphp.org
 * @package flashservices
 * @subpackage util
 * @version $Id: Amfphp_Core_Amf_Header.php,v 1.4 2005/07/05 07:40:53 pmineault Exp $
 */

class Amfphp_Core_Amf_Header
{
	/**
	 * Name is the string name of the header key
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Required is a boolean determining whether the remote system
	 * must understand this header in order to operate.  If the system
	 * does not understand the header then it should not execute the
	 * method call.
	 *
	 * @var boolean
	 */
	public $required;

	/**
	 * data is the actual object data of the header key
	 *
	 * @var mixed
	 */
	public $data;

	/**
	 * Amfphp_Core_Amf_Header is the Constructor function for the Amfphp_Core_Amf_Header data type.
	 */
	public function __construct($name = "", $required = false, $data = null)
	{
		$this->name = $name;
		$this->required = $required;
		$this->data = $data;
	}
}

?>