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

package org.amfphp.vo
{
	import flash.net.NetConnection;
	import flash.net.ObjectEncoding;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	import flash.utils.IExternalizable;
 
	[Bindable]
	[RemoteClass(alias="vo.Preferences")]
	
	/**
	 * This class is used to store preferences data in a SharedObject. It implements the IExternalizable interface to 
	 * allow the data to be stored as a serialized object.
	 * 
	 * @see flash.utils.IExternalizable
	 * 
	 * @author Danny Kopping - danny@ria-coder.com
	 * 
	 */
	public class Preferences implements IExternalizable
	{
		/**
		 * The stored endpoint to use to call the remote AMFPHP services
		 */		
		public var endpoint:String;
		/**
		 * The AMF encoding to use
		 * 
		 * @default flash.net.ObjectEncoding.AMF3
		 */
		public var encoding:uint;
		
		/**
		 * The default page to open when an AMF call returns data successfully
		 */
		public var defaultPage:String;
		
		/**
		 * Reads serialized data from the SharedObject and assimilates the values into this Value Object
		 */
		public function readExternal(input:IDataInput):void
		{
			endpoint = input.readUTF();
			encoding = input.readUnsignedInt();
			defaultPage = input.readUTF();
			
		}
		
		/**
		 * Serializes the data from this Value Object and writes it to the SharedObject
		 */
		public function writeExternal(output:IDataOutput):void
		{
			output.writeUTF(endpoint);
			output.writeUnsignedInt(encoding);
			output.writeUTF(defaultPage);
		}
	}
}