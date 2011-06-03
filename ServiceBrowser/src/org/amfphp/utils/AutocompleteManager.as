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
	import flash.events.EventDispatcher;
	import flash.utils.Dictionary;
	
	import mx.collections.ArrayCollection;
	import mx.events.PropertyChangeEvent;

	/**
	 * Handles the autocompletion for the arguments in the methods
	 * 
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class AutocompleteManager extends EventDispatcher
	{
		private static var _instance:AutocompleteManager;
		private var _options:Dictionary = new Dictionary();
		
		public static function get instance():AutocompleteManager
		{
			if(!_instance)
				_instance = new AutocompleteManager();
				
			return _instance;
		}
		
		/**
		 * Initializes the autocompletion with default values
		 */
		public function initialize():void
		{
			options["all"] = [true, false, 0, 1, "{}", "[]", "null"];
		}
		
		/**
		 * Get an array containing the default autocompletion options and the options relating to the current method
		 */
		public function getOptionsArray(identifier:String):Array
		{			
			if(options[identifier])
			{
				var current:ArrayCollection = new ArrayCollection(options["all"]);
				for each(var value:Object in options[identifier])
				{
					if(current.getItemIndex(value) == -1)
						options["all"].push(value);
				}
			}
				
			return options["all"];
		}
		
		[Bindable(event="propertyChange")]
		/**
		 * Gets the autocompletion options
		 */
		public function get options():Dictionary
		{
			return _options;
		}
		
		/**
		 * Sets the autocompletion options
		 */
		public function set options(value:Dictionary):void
		{
			var oldValue:Dictionary = _options;
			if(oldValue != value)
			{
				_options = value;
				dispatchEvent(PropertyChangeEvent.createUpdateEvent(this, "options", oldValue, _options));
			}
		}
	}
}