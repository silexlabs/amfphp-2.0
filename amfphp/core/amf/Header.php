<?php
/**
 * Amfphp_Core_Amf_Header is a data type that represents a single header passed via AMF
 *
 * Amfphp_Core_Amf_Header encapsulates the different AMF keys.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 AMFphp.org
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
	 * Value is the actual object value of the header key
	 *
	 * @var mixed
	 */
	public $value;

	/**
	 * Amfphp_Core_Amf_Header is the Constructor function for the Amfphp_Core_Amf_Header data type.
	 */
	public function __construct($name = "", $required = false, $value = null)
	{
		$this->name = $name;
		$this->required = $required;
		$this->value = $value;
	}
}

?>