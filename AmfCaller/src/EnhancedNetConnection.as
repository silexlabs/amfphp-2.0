package 
{
	import flash.events.AsyncErrorEvent;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;
	import flash.net.ObjectEncoding;
	import flash.net.Responder;
	import flash.utils.ByteArray;
	import flash.utils.getTimer;
	
	
	/**
	 * NetConnection with events.
	 * */
	public class EnhancedNetConnection extends NetConnection
	{
		/**
		 * extra events for the nc. there are no events for onResult and onStatus, so create some here. 
		 * */ 
		static public const EVENT_ONRESULT:String = "onResult";
		static public const EVENT_ONSTATUS:String = "onStatus";
		
		private var _startTime:int;
		private var _duration:int;
		
		private function onResult(res:Object):void{
			_duration = getTimer() - _startTime; 
			dispatchEvent(new ObjEvent(EVENT_ONRESULT, res)); 
		}
		
		private function onStatus(statusObj:Object):void{
			_duration = getTimer() - _startTime; 
			trace("onStatus. faultcode :" + statusObj.faultCode + "\r faultDetail : " + statusObj.faultDetail + "\r faultString : " + statusObj.faultString);
			dispatchEvent(new ObjEvent(EVENT_ONSTATUS, statusObj)); 
		}
		
		/**
		 * like call, but with out responder, and events instead. 
		 * */
		public function callWithEvents(command:String, ... args):void{
			trace("call command " + command);
			var callArgs:Array = new Array(command, new Responder(onResult, onStatus));
			for each(var arg:* in args){
				callArgs.push(arg);
				
			}	
			call.apply(this, callArgs);
			_startTime = getTimer();		
						
			
		}
		
		public function get duration():int{
			return duration;
		}
						
		
	}
}