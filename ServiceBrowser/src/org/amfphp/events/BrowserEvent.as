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

package org.amfphp.events
{
	import flash.events.Event;

	/**
	 * Event class with various static constants that control the application
	 * 
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class BrowserEvent extends Event
	{
		public static const DESCRIBE_SERVICE:String = "describeService";
		public static const SERVICE_SELECTED:String = "serviceSelected";
		public static const PREFERENCES_UPDATED:String = "preferencesUpdated";
		public static const REFRESH_SERVICES:String = "refreshServices";
		public static const KILL_REQUEST:String = "killRequest";
		
		/**
		 * The information to carry along with the event
		 */
		public var information:Object;
		
		public function BrowserEvent(type:String, information:Object=null, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			
			if(information)
				this.information = information;
		}
	}
}