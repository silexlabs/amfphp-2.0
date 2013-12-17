package
{
	
	
	import flash.display.Sprite;
	import flash.events.TimerEvent;
	import flash.external.ExternalInterface;
	import flash.net.NetConnection;
	import flash.net.Responder;
	import flash.net.getClassByAlias;
	import flash.net.registerClassAlias;
	import flash.utils.Dictionary;
	import flash.utils.Timer;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	import flash.utils.getTimer;
	
	
	
	/**
	 * provides AMF calling functionality to the service browser via External Interface. 
	 * exposes a "call" method, a "isAlive" method, and uses 1 callback: "onResult"
	 * calls onAmfCallerLoaded once loading is done
	 * */
	public class AmfCaller extends Sprite
	{
		private var numUsedDummyClasses:int = 0;
		private var type2Class:Dictionary = new Dictionary();
		
		private var enhancedNetConnections:Array;
		
		private var isRepeating:Boolean;
		
		private var repeatingCallArgs:Array;
		
		private var callCounter:uint;
				
		private var timeAtLastMeasure:uint; 
		
		private var measureTimer:Timer;
		
		private var resultJsCallBackName:String;
		
		public function AmfCaller()
		{
			ExternalInterface.addCallback("call", call);
			ExternalInterface.addCallback("isAlive", isAlive);
			ExternalInterface.addCallback("repeat", repeat);
			ExternalInterface.addCallback("stopRepeat", stopRepeat);
			
			//need this to make sure compiler includes dummy classes
			var dummyRef:Class = Dummy0;
			dummyRef = Dummy1;
			dummyRef = Dummy2;
			dummyRef = Dummy3;
			dummyRef = Dummy4;
			dummyRef = Dummy5;
			dummyRef = Dummy6;
			dummyRef = Dummy7;
			dummyRef = Dummy8;
			dummyRef = Dummy9;
			dummyRef = Dummy10;
			dummyRef = Dummy11;
			dummyRef = Dummy12;
			dummyRef = Dummy13;
			dummyRef = Dummy14;
			dummyRef = Dummy15;
			dummyRef = Dummy16;
			dummyRef = Dummy17;
			dummyRef = Dummy18;
			dummyRef = Dummy19;
			measureTimer = new Timer(1000);
			measureTimer.addEventListener(TimerEvent.TIMER, measureTimerHandler);
			ExternalInterface.call("onAmfCallerLoaded");
			
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
		public function call(url:String, command:String, parameters:Array, resultJsCallBackName:String):void{
			var netConnection:NetConnection = new NetConnection();
			netConnection.connect(url);
			var callArgs:Array = new Array(command, new Responder(resultHandler, resultHandler));
			type2Class = new Dictionary();
			for each(var param:* in parameters){
				callArgs.push(convertObjectUsingExplicitType(param));
				
			}	
			this.resultJsCallBackName = resultJsCallBackName;
			netConnection.call.apply(netConnection, callArgs);
		}
		
		/**
		 * make concurrent repeated calls to server.
		 * call stopRepeat to stop
		 * */
		public function repeat(url:String, command:String, numConcurrentCalls:uint, parameters:Array):void{
			callCounter = 0;
			enhancedNetConnections = new Array();
			repeatingCallArgs = new Array(command);
			type2Class = new Dictionary();
			for each(var param:* in parameters){
				repeatingCallArgs.push(convertObjectUsingExplicitType(param));
				
			}	
			for(var i:int = 0; i < numConcurrentCalls; i++){
				var enc:EnhancedNetConnection = new EnhancedNetConnection();
				enhancedNetConnections.push(enc);
				enc.connect(url);
				enc.callWithEvents.apply(enc, repeatingCallArgs);
				enc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, encResultHandler, false, 0, true);
				enc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, encResultHandler, false, 0, true);
			}
			
			isRepeating = true;
			timeAtLastMeasure = getTimer();
			measureTimer.start();
		}
		
		private function encResultHandler(event:ObjEvent):void{
			callCounter++;
			if(isRepeating){
				var enc:EnhancedNetConnection = EnhancedNetConnection(event.target);
				enc.callWithEvents.apply(enc, repeatingCallArgs);
			}
		}	
		/**
		 * measure number of executed calls since last timer
		 * and call onRepeatResult JS callback.
		 * */
		private function measureTimerHandler(event:TimerEvent):void{
			var now:int = getTimer();
			var callsPerSecond:Number = callCounter / (now - timeAtLastMeasure) * 1000;
			ExternalInterface.call("onRepeatResult", callsPerSecond);
			timeAtLastMeasure = now;
			callCounter = 0;
			if(!isRepeating){
				measureTimer.stop();
			}
			
		}
		
		/**
		 * called from js to stop load testing.
		 * 
		 * */
		public function stopRepeat():void{
			isRepeating = false;
		}

		/**
		 * callback used both for error and success. calls onResult in JS.
		 * */
		private function resultHandler(obj:Object):void{
			if(resultJsCallBackName){
				ExternalInterface.call(this.resultJsCallBackName, obj);
			}else{
				ExternalInterface.call("onResult", obj);
			}
			
		}
		
		
		/**
		 * replaces an object marked with _explicitType by a Dummy class, and registers the Dummy class with an alias set to _explicitType.
		 * Yes this is absolutely bending over backwards, and is limited to 20 different explicit types.
		 * A possible way to override this would be to manipulate the byte code directly but it's more trouble than it's worth.
		 * Any suggestion on how to do this in a cleaner fashion is welcome.
		 * */
		private function convertObjectUsingExplicitType(obj:*):*{
			var type:String = getQualifiedClassName(obj);
			if((type != "Array") && (type != "Object")){
				return obj;
			}
			
			var explicitType:String = obj["_explicitType"]; 
			if(!explicitType){
				return obj;
			}

			//if we're here it means that the object needs to be replaced with one with which we can call registerClassAlias
			//so replace by a typed one
			var dummyClass:Class = null;
			
			if(!type2Class[explicitType]){
				dummyClass = getDefinitionByName("Dummy" + numUsedDummyClasses) as Class;
				type2Class[explicitType] = dummyClass;
				numUsedDummyClasses++;
			}else{
				dummyClass = type2Class[explicitType];
			}
			var ret:Object = new dummyClass();
			registerClassAlias(explicitType, dummyClass);
			
			for(var key:String in obj){
				if(key == "_explicitType"){
					continue;
				}
				var subObj:* = obj[key];
				
				ret[key] = convertObjectUsingExplicitType(subObj); 					
			}
			
			return ret;
		}
		
	}
	
}

