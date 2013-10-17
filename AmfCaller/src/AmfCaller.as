package
{
	import flash.display.Sprite;
	import flash.external.ExternalInterface;
	import flash.net.NetConnection;
	import flash.net.Responder;
	import flash.net.getClassByAlias;
	
	/**
	 * provides AMF calling functionality to the service browser via External Interface. 
	 * exposes a "call" method, a "isAlive" method, and uses 1 callback: "onResult"
	 * */
	public class AmfCaller extends Sprite
	{
		public function AmfCaller()
		{
			ExternalInterface.addCallback("call", call);
			ExternalInterface.addCallback("isAlive", isAlive);
		}
		
		/**
		 * a quick way for JS to check that AmfCaller is properly loaded and avaliable
		 * */
		public function isAlive():Boolean{
			return true;
		}
		/**
		 * make an AMF call
		 * */
		public function call(url:String, command:String, parameters:Array):void{
			var netConnection:NetConnection = new NetConnection();
			netConnection.connect(url);
			var callArgs:Array = new Array(command, new Responder(resultHandler, resultHandler));
			for each(var param:* in parameters){
				callArgs.push(param);
				
			}	
			netConnection.call.apply(netConnection, callArgs);
		}

		/**
		 * callback used both for error and success. calls onResult in JS.
		 * */
		private function resultHandler(obj:Object):void{
			
			ExternalInterface.call("onResult", obj);
			
		}
		
	}
}