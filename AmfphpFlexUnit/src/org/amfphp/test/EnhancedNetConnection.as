package org.amfphp.test
{
	import flash.events.AsyncErrorEvent;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;
	import flash.net.ObjectEncoding;
	import flash.net.Responder;
	import flash.utils.ByteArray;
	
	import flexUnitTests.TestConfig;
	
	
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
		
		
		static private var requestCounter:uint = 0;
		
		
		public function EnhancedNetConnection()
		{
			super();
			addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);	
			addEventListener(AsyncErrorEvent.ASYNC_ERROR, onAsyncError);	
			addEventListener(IOErrorEvent.IO_ERROR, onIoError);	
			addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);	
		}
		
		public override function connect(command:String, ...parameters):void{
			var connectRet:Boolean = super.connect(command);
			trace("connect " + connectRet +  ", command : " + command);
		}

		
		private function onNetStatus(event:NetStatusEvent):void{
			trace(event.toString() + "\r info code : " + event.info.code + "\r info description : " + event.info.description + "\r info details : " + event.info.details + "\r info level :" +  event.info.level );
			
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
			dispatchEvent(new ObjEvent(EVENT_ONRESULT, res)); 
		}
		
		private function onStatus(statusObj:Object):void{
			trace("onStatus. faultcode :" + statusObj.faultCode + "\r faultDetail : " + statusObj.faultDetail + "\r faultString : " + statusObj.faultString);
			dispatchEvent(new ObjEvent(EVENT_ONSTATUS, statusObj)); 
		}
		
		/**
		 * like call, but with out responder, and events instead. necessary for use with flex unit
		 * support for doing without server added
		 * */
		public function callWithEvents(command:String, ... args):void{
			trace("call command " + command);
			requestCounter++;
			var callArgs:Array = new Array(command, new Responder(onResult, onStatus));
			for each(var arg:* in args){
				callArgs.push(arg);
				
			}	
			call.apply(this, callArgs);
				
					
			
		}
		
		public function onIOError(event:IOErrorEvent):void
		{
			trace(event.toString());
			
		}				
		
	}
}