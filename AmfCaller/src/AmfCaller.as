package
{
	
	import flash.display.Sprite;
	import flash.external.ExternalInterface;
	import flash.net.NetConnection;
	import flash.net.Responder;
	import flash.net.getClassByAlias;
	import flash.net.registerClassAlias;
	import flash.utils.Dictionary;
	import flash.utils.getDefinitionByName;
	import flash.utils.getQualifiedClassName;
	
	
	
	/**
	 * provides AMF calling functionality to the service browser via External Interface. 
	 * exposes a "call" method, a "isAlive" method, and uses 1 callback: "onResult"
	 * */
	public class AmfCaller extends Sprite
	{
		private var numUsedDummyClasses:int = 0;
		private var type2Class:Dictionary = new Dictionary();
		
		public function AmfCaller()
		{
			ExternalInterface.addCallback("call", call);
			ExternalInterface.addCallback("isAlive", isAlive);
			
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
				callArgs.push(convertObjectUsingExplicitType(param));
				
			}	
			netConnection.call.apply(netConnection, callArgs);
		}

		/**
		 * callback used both for error and success. calls onResult in JS.
		 * */
		private function resultHandler(obj:Object):void{
			
			ExternalInterface.call("onResult", obj);
			
		}
		
		
		/**
		 * replaces an object marked with _explicitType by a Dummy class, and registers the Dummy class with an alias set to _explicitType.
		 * Yes this is absolutely bending over backwards.
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

