package org.amfphp.test
{
	import flash.desktop.NativeProcess;
	import flash.desktop.NativeProcessStartupInfo;
	import flash.events.AsyncErrorEvent;
	import flash.events.DataEvent;
	import flash.events.IOErrorEvent;
	import flash.events.NativeProcessExitEvent;
	import flash.events.NetStatusEvent;
	import flash.events.ProgressEvent;
	import flash.events.SecurityErrorEvent;
	import flash.filesystem.File;
	import flash.filesystem.FileMode;
	import flash.filesystem.FileStream;
	import flash.net.FileReference;
	import flash.net.NetConnection;
	import flash.net.ObjectEncoding;
	import flash.net.Responder;
	import flash.utils.ByteArray;
	
	
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
		
		static public const FAKE:Boolean = true;
		private var process:NativeProcess;
		
		public function EnhancedNetConnection()
		{
			super();
			addEventListener(NetStatusEvent.NET_STATUS, onNetStatus);	
			addEventListener(AsyncErrorEvent.ASYNC_ERROR, onAsyncError);	
			addEventListener(IOErrorEvent.IO_ERROR, onIoError);	
			addEventListener(SecurityErrorEvent.SECURITY_ERROR, onSecurityError);	
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
		 * */
		public function callWithEvents(command:String, ... args):void{
			if(!FAKE){
				var callArgs:Array = new Array(command, new Responder(onResult, onStatus));
				for each(var arg:* in args){
					callArgs.push(arg);
					
				}	
				call.apply(this, callArgs);
				
			}else{
				var data:ByteArray = new ByteArray();
				data.objectEncoding = objectEncoding;	// Set the AMF encoding
				
				data.writeByte(0x00);
				data.writeByte(0x03);	// Set the Flash Player type (in this case, Flash Player 9+)
				
				//Write the headers
				data.writeByte(0x00);	
				data.writeByte(0x00);	// Set the number of headers

				//Write one body
				data.writeByte(0x00);
				data.writeByte(0x01);
				
				//Write the target (null)
				data.writeByte(0x00);
				data.writeByte(command.length);
				data.writeUTFBytes(command);
				
				//Now write the response handler
				data.writeByte(0x00);
				data.writeByte(0x02);
				data.writeUTFBytes("/1");
				
				//Write the number of bytes in the body
				data.writeByte(0x00);
				data.writeByte(0x00);
				data.writeByte(0x00);
				data.writeByte(0x00); //amfphp doesn't read this, so no matter
				
				
				//Write the AMF3 bytecode
				if (objectEncoding == ObjectEncoding.AMF3)
				{
					data.writeByte(0x11);
				}
				//Then the object
				data.writeObject(args);
				
				//write message to file
				var fs : FileStream = new FileStream();
				var targetFile : File = File.documentsDirectory.resolvePath('test.amf');
				fs.open(targetFile, FileMode.WRITE);
				fs.writeBytes(data);
				fs.close();
				
				//send to php
				var nativeProcessStartupInfo:NativeProcessStartupInfo = new NativeProcessStartupInfo();
				var file:File = File.applicationDirectory.resolvePath("/php-5.3.2/bin/php.dSYM");
				nativeProcessStartupInfo.executable = file;
				
				var processArgs:Vector.<String> = new Vector.<String>();
				processArgs[0] = "/Users/arielsommeria-klein/Documents/workspaces/workspaceNetbeans/amfphp-2.0/AmfphpFlexUnit/src/test.php";
				//processArgs[1] = "'echo \"blq\"'";
//				processArgs[1] = "'file_put_contents(\"zer\",\"eeee\");'";
				nativeProcessStartupInfo.arguments = processArgs;
				
				process = new NativeProcess();
				process.start(nativeProcessStartupInfo);
				process.addEventListener(ProgressEvent.STANDARD_OUTPUT_DATA, onOutputData);
				process.addEventListener(ProgressEvent.STANDARD_ERROR_DATA, onErrorData);
				process.addEventListener(NativeProcessExitEvent.EXIT, onExit);
				process.addEventListener(IOErrorEvent.STANDARD_OUTPUT_IO_ERROR, onIOError);
				process.addEventListener(IOErrorEvent.STANDARD_ERROR_IO_ERROR, onIOError);
			}				
			
		}
		public function onOutputData(event:ProgressEvent):void
		{
			trace('bytes ' + process.standardOutput.bytesAvailable); 
			var rawData:ByteArray = new ByteArray();
			process.standardOutput.readBytes(rawData);
//			rawData.objectEncoding = ObjectEncoding.AMF3;
			
			//Determine if data is valid
			
			
			if (rawData[0] == 0x00)
			{
				var numHeaders:uint = rawData[2] * 256 + rawData[3];
				rawData.position = 4;
				for (var i:int = 0; i < numHeaders; i++)
				{
					var strlen:int = rawData.readUnsignedShort();
					var key:String = rawData.readUTFBytes(strlen);
					var required:Boolean = rawData.readByte() == 1;
					var len:int = rawData.readUnsignedInt();
					rawData.position += len; //Just skip for now
				}
				var numBodies:uint = rawData.readUnsignedShort();
				for (var i:int = 0; i < numBodies; i++)
				{
					var strlen:int = rawData.readUnsignedShort();
					var target:String = rawData.readUTFBytes(strlen);
					
					strlen = rawData.readUnsignedShort();
					var response:String = rawData.readUTFBytes(strlen);
					
					var bodyLen:uint = rawData.readUnsignedInt();
					
					//var key:String = rawData.readUTFBytes(strlen);
					//var required:Boolean = rawData.readByte() == 1;
					//var len:int = rawData.readUnsignedInt();
					
					if (objectEncoding == ObjectEncoding.AMF3)
					{
						var amf3Byte:uint = rawData.readUnsignedByte();
						rawData.objectEncoding = ObjectEncoding.AMF3;
					}
					
					var bodyVal:Object = rawData.readObject();
					rawData.objectEncoding = ObjectEncoding.AMF0;
					
					var sendEvent:ObjEvent;
					if (target == '/1/onDebugEvents')
					{
						//Look at the bodyVal
						for (var j:uint = 0; j < bodyVal[0].length; j++)
						{
							if (bodyVal[0][j].EventType == 'trace')
							{
								//Bingo, we got trace
//								traceMessages = bodyVal[0][j].messages;
							}
							else if (bodyVal[0][j].EventType == 'profiling')
							{
								//Bingo, we got trace
	//							profiling = bodyVal[0][j];
							}
						}
					}
					else if (target == '/1/onResult')
					{
						sendEvent = new ObjEvent(EVENT_ONRESULT, bodyVal.body);
						
					}
					else if (target == '/1/onStatus')
					{
						trace("onStatus. faultcode :" + bodyVal.faultCode + "\r faultDetail : " + bodyVal.faultDetail + "\r faultString : " + bodyVal.faultString);
						sendEvent = new ObjEvent(EVENT_ONSTATUS, bodyVal); 
						//dispatchEvent(fe);
					}
				}
			}
			else
			{
				//Create a new Fault event
				rawData.position = 0;
				var errorMessage:String = rawData.readUTFBytes(rawData.length);
				sendEvent = new ObjEvent(EVENT_ONSTATUS, "Invalid AMF message" +  errorMessage);
				//dispatchEvent(fe);
			}
			
			var totalTime:uint = 0;
			if (rawData.bytesAvailable == 2)
			{
				totalTime = rawData.readUnsignedShort();
			}
			else
			{
			}
			
			dispatchEvent(sendEvent);			
		}
		
		public function onErrorData(event:ProgressEvent):void
		{
			trace("ERROR -", process.standardError.readUTFBytes(process.standardError.bytesAvailable)); 
		}
		
		public function onExit(event:NativeProcessExitEvent):void
		{
			trace("Process exited with ", event.exitCode);
		}
		
		public function onIOError(event:IOErrorEvent):void
		{
			trace(event.toString());
		}				
		
	}
}