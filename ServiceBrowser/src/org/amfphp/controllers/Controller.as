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

package org.amfphp.controllers
{
	import flash.events.EventDispatcher;
	import flash.net.ObjectEncoding;
	import flash.net.SharedObject;
	
	import mx.core.Application;
	import mx.utils.URLUtil;
	
	import org.amfphp.utils.DefaultPage;
	import org.amfphp.utils.ServerUtils;
	import org.amfphp.vo.Preferences;
	
	/**
	 * A singleton that controls the general application flow
	 * 
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class Controller extends EventDispatcher
	{
		private static var _instance:Controller;
		
		/**
		 * AMFPHP gateway URL
		 */
		public var url:String;
		
		/**
		 * Local storage
		 */
		public var storage:SharedObject;
		
		/**
		 * Stored preferences
		 */
		public var preferences:Preferences;
		
		/**
		 * Returns an instance of the <code>Controller</code>
		 */
		public static function get instance():Controller
		{
			if(!_instance)
				_instance = new Controller();
				
			return _instance;
		}
		
		/**
		 * Initialize this Singleton by getting the URL of the application, fetches the preferences stored locally and 
		 * stores the preferences in the <code>preferences</code> property
		 */
		public function initialize():void
		{
			url = Application.application.loaderInfo.url;
			storage = SharedObject.getLocal(URLUtil.getServerName(url));
			preferences = storage.data.preferences ? storage.data.preferences : new Preferences();
		}
		
		/**
		 * Sets the AMFPHP gateway URL
		 */
		public function set endpoint(value:String):void
		{
			if(preferences.endpoint != value)
				preferences.endpoint = value;
		}
		
		/**
		 * Gets the AMFPHP gateway URL
		 * 
		 * @see org.amfphp.utils.ServerUtils
		 */
		public function get endpoint():String
		{
			return preferences.endpoint ? preferences.endpoint : ServerUtils.getEndpoint();
		}
		
		/**
		 * Sets the AMF object encoding
		 * 
		 * @see flash.net.ObjectEncoding
		 */
		public function set encoding(value:uint):void
		{
			if(preferences.encoding != value)
				preferences.encoding = value;
		}
		
		/**
		 * Gets the AMF object encoding
		 * 
		 * @see flash.net.ObjectEncoding
		 * @default flash.net.ObjectEncoding#AMF3
		 */
		public function get encoding():uint
		{
			return preferences.encoding ? preferences.encoding : ObjectEncoding.AMF3;
		}
		
		/**
		 * Sets the default tab to open once an AMF request returns successfully
		 */
		public function set defaultPage(value:String):void
		{
			if(preferences.defaultPage != value)
				preferences.defaultPage = value;
		}
		
		/**
		 * Gets the default tab to open once an AMF request returns successfully
		 * 
		 * @default org.amfphp.utils.DefaultPage#RESULTS
		 */
		public function get defaultPage():String
		{
			return preferences.defaultPage ? preferences.defaultPage : DefaultPage.RESULTS;
		}
		
		/**
		 * Saves the preferences to the SharedObject
		 */
		public function setPreferences(preferences:Preferences):void
		{
			storage.data.preferences = preferences;
	        storage.flush();
		}
	}
}