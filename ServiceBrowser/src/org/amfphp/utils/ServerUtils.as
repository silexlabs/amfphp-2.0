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

package org.amfphp.utils
{
	import org.amfphp.controllers.Controller;
	
	/**
	 * This static class contains utilities relating to the server
	 * 
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class ServerUtils
	{		
		/**
		 * Guess the most likely location of the AMFPHP gateway URL
		 */
		public static function getEndpoint():String
		{
        	var endpoint:String = "";
            
            var parts:Array = Controller.instance.url.split("/");
            for each(var part:String in parts)
            {
            	if(part != "browser")
            		endpoint += part + "/";
            	else
            	{
            		endpoint += "gateway.php";
            		break;
            	}
            }
            
            return endpoint;
		}
	}
}