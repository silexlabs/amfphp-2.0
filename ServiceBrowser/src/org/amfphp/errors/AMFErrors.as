//////////////////////////////////////////////////////////////////////////////////
//																				//
//		This file is part of AMFPHP												//
//    																			//
//		It is released under the GPL License:									//
//																				//
//		This program is free software; you can redistribute it and/or			//
//		modify it under the terms of the GNU General Public License (GPL)		//
//		as published by the Free Software Foundation; either version 2			//
//		of the License, or (at your option) any later version.					//
//																				//
//		This program is distributed in the hope that it will be useful,			//
//		but WITHOUT ANY WARRANTY; without even the implied warranty of			//
//		MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the			//
//		GNU General Public License for more details.							//
//   																			//
//		To read the license please visit http://www.gnu.org/copyleft/gpl.html	//
//																				//
//////////////////////////////////////////////////////////////////////////////////

package org.amfphp.errors
{
	/**
	 * Contains static constants relating to the various errors that can occur during an AMF request
	 * 
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class AMFErrors
	{
		/**
		 * Script error or otherwise faulty error
		 */
		public static const INVALID_AMF_MESSAGE:String = "INVALID_AMF_MESSAGE";
		
		/**
		 * Misplaced folder of services
		 */
		public static const AMFPHP_CLASSPATH_NOT_FOUND:String = "AMFPHP_CLASSPATH_NOT_FOUND";
		
		/**
		 * Misplaced service
		 */
		public static const AMFPHP_FILE_NOT_FOUND:String = "AMFPHP_FILE_NOT_FOUND";
		
		/**
		 * Service cannot be included because of script error
		 */
		public static const AMFPHP_FILE_NOT_INCLUDED:String = "AMFPHP_FILE_NOT_INCLUDED";
		
		/**
		 * Class name for service does not match service name
		 */
		public static const AMFPHP_CLASS_NOT_FOUND:String = "AMFPHP_CLASS_NOT_FOUND";
		
		/**
		 * Method in service does not exist
		 */
		public static const AMFPHP_INEXISTANT_METHOD:String = "AMFPHP_INEXISTANT_METHOD";
		
		/**
		 * Method is private; only public functions can be invoked via RPC
		 */
		public static const AMFPHP_PRIVATE_METHOD:String = "AMFPHP_PRIVATE_METHOD";
		
		/**
		 * Insufficient authentication privileges or incorrect authentication details provided
		 */
		public static const AMFPHP_AUTHENTICATE_ERROR:String = "AMFPHP_AUTHENTICATE_ERROR";
		
		/**
		 * Runtime script error in service
		 */
		public static const AMFPHP_RUNTIME_ERROR:String = "AMFPHP_RUNTIME_ERROR";
		
		/**
		 * Build error occurs for whatever reason
		 */
		public static const AMFPHP_BUILD_ERROR:String = "AMFPHP_BUILD_ERROR";
		
		/**
		 * Cannot include class
		 */
		public static const AMFPHP_INCLUDE_ERROR:String = "AMFPHP_INCLUDE_ERROR";
	}
}