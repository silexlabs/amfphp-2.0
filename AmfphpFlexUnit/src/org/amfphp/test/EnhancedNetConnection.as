package org.amfphp.test
{
	import flash.events.AsyncErrorEvent;
	import flash.events.DataEvent;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;
	import flash.net.Responder;

	/**
	 * better class for tests, with extra events, traces, and no responder
	 * */
	public class EnhancedNetConnection extends NetConnection
	{
		/**
		 * extra events for the nc. there are no events for onResult and onStatus, so create some here. 
		 * We need these events for async testing
		 * */ 
		static public const EVENT_ONRESULT:String = "onResult";
		static public const EVENT_ONSTATUS:String = "onStatus";
		
		public function EnhancedNetConnection()
		{
			super();
			addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);	
			addEventListener(AsyncErrorEvent.ASYNC_ERROR, onAsyncError);	
			addEventListener(IOErrorEvent.IO_ERROR, onIoError);	
			addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);	
		}
		
		private function onNetStatus(event:NetStatusEvent):void{
			trace(event.toString() + "\r" + event.info.code + "\r" + event.info.description + "\r" + event.info.details + "\r" +  event.info.level );
			
		}
		
		private function onAsyncError(event:AsyncErrorEvent):void{
			trace(event.toString());
		}
		
		private function onIoError(event:IOErrorEvent):void{
			trace(event.toString());
			
		}
		
		private function onSecurityError(event:SecurityErrorEvent):void{
			trace(event.toString());
			
		}
		
		private function onResult(res:Object):void{
			if(res){
				dispatchEvent(new DataEvent(EVENT_ONRESULT, false, false, res.toString())); 
			}else{
				dispatchEvent(new DataEvent(EVENT_ONRESULT, false, false, null)); 
				
			}
		}
		
		private function onStatus(statusObj:Object):void{
			if(statusObj is String){
				dispatchEvent(new DataEvent(EVENT_ONSTATUS, false, false, statusObj as String)); 
			}else{
				dispatchEvent(new DataEvent(EVENT_ONSTATUS, false, false, statusObj.toString())); 
				
			}
		}
		
		/**
		 * like call, but with out responder
		 * */
		public function simpleCall(command:String, ... args):void{
			call(command, new Responder(onResult, onStatus), args);
		}
	}
}