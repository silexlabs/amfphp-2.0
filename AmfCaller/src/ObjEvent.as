package 
{
	import flash.events.Event;
	
	/**
	 * event that can be used to carry some arbitrary object. Used by EnhancedNetConnection
	 * */
	public class ObjEvent extends Event
	{
		private var _obj:Object;
		
		public function ObjEvent(type:String, obj:Object, bubbles:Boolean=false, cancelable:Boolean=false)
		{
			super(type, bubbles, cancelable);
			_obj = obj;
		}

		public function get obj():Object
		{
			return _obj;
		}

	}
}