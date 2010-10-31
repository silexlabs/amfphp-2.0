<?php
	/**
	 * AMFBody is a data type that encapsulates all of the various properties a body object can have.
	 *
	 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
	 * @copyright (c) 2003 amfphp.org
	 * @package flashservices
	 * @subpackage util
	 * @version $Id: AMFBody.php,v 1.6 2005/07/05 07:40:53 pmineault Exp $
	 */

	class MessageBody
	{

		var $targetURI = "";
		var $responseURI = "";
		var $uriClassPath = "";
		var $classPath = "";
		var $className = "";
		var $methodName = "";
		var $responseTarget = "null";
		var $noExec = false;

		var $_value = NULL;
		var $_results = NULL;
		var $_classConstruct = NULL;
		var $_specialHandling = NULL;
		var $_metaData = array();

		/**
		 * AMFBody is the Contstructor method for the class
		 */
		public function __construct($targetURI = "", $responseIndex = "", $value = "")
		{
			$GLOBALS['amfphp']['lastMethodCall'] = $responseIndex;
			$this->responseIndex = $responseIndex;
			$this->targetURI = $targetURI;
			$this->responseURI = $this->responseIndex . "/onStatus"; // default to the onstatus method
			$this->setValue($value);
		}

		/**
		 * setter for the results from the process execution
		 *
		 * @param mixed $results The returned results from the process execution
		 */
		public function setResults($result)
		{
			$this->_results = $result;
		}

		/**
		 * getter for the result of the process execution
		 *
		 * @return mixed The results
		 */
		public function getResults()
		{
			return $this->_results;
		}

		/**
		 * setter for the class construct
		 *
		 * @param object $classConstruct The instance of the service class
		 */
		public function setClassConstruct($classConstruct)
		{
			$this->_classConstruct = $classConstruct;
		}

		/**
		 * getter for the class construct
		 *
		 * @return object The class instance
		 */
		public function getClassConstruct()
		{
			return $this->_classConstruct;
		}

		/**
		 * setter for the value property
		 *
		 * @param mixed $value The value of the body object
		 */
		public function setValue($value)
		{
			$this->_value = $value;
		}

		/**
		 * getter for the value property
		 *
		 * @return mixed The value property
		 */
		public function getValue()
		{
			return $this->_value;
		}

		/**
		 * Set special handling type for this body
		 */
		public function setSpecialHandling($type)
		{
			$this->_specialHandling = $type;
		}

		/**
		 * Get special handling type for this body
		 */
		public function getSpecialHandling()
		{
			return $this->_specialHandling;
		}

		/**
		 * Check if this body is handled special against an array of special cases
		 */
		public function isSpecialHandling($against = NULL)
		{
			if ($against !== NULL)
			{
				return in_array($this->_specialHandling, $against);
			}
			else
			{
				return ($this->_specialHandling != NULL);
			}
		}

		public function setMetaData($key, $val)
		{
			$this->_metaData[$key] = $val;
		}

		public function getMetaData($key)
		{
			if (isset($this->_metaData[$key]))
			{
				return $this->_metaData[$key];
			}
			else
			{
				return NULL;
			}
		}
	}
?>