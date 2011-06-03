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
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.net.ObjectEncoding;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.net.URLRequestHeader;
	import flash.net.URLRequestMethod;
	import flash.utils.ByteArray;
	import flash.utils.getTimer;
	
	import mx.messaging.messages.RemotingMessage;
	import mx.rpc.Fault;
	import mx.rpc.events.AbstractEvent;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.InvokeEvent;
	import mx.rpc.events.ResultEvent;
	
	import org.amfphp.events.BrowserEvent;
	
	[Bindable]
	
	[Event(name="result", type="mx.rpc.events.ResultEvent")]
	[Event(name="fault", type="mx.rpc.events.FaultEvent")]
	[Event(name="invoke", type="mx.rpc.events.InvokeEvent")]
	[Event(name="killRequest", type="org.amfphp.events.BrowserEvent")]
	/**
	 * Handle the sending and receipt of raw AMF data
	 * 
	 * @author Patrick Mineault
	 * @author Danny Kopping danny@ria-coder.com
	 */
	public class RawAMFService extends EventDispatcher
	{
		public var endpoint:String;
		public var traceMessages:Array;
		public var operation:String;

		private var loader:URLLoader;

		private var time:Number;

		public var diagnostic:Object;
		public var profiling:Object;

		public var encoding:uint = ObjectEncoding.AMF3;
		
		public var isRunning:Boolean = false;

		/**
		 * Constructs a new <code>RawAMFService</code> object
		 */
		public function RawAMFService()
		{
			loader = new URLLoader();
			loader.addEventListener('complete', readData);
		}

		/**
		 * Send the AMF data
		 */
		public function send(className:String, methodName:String, args:Array):void
		{
			dispatchEvent(new InvokeEvent(InvokeEvent.INVOKE));
			
			diagnostic = {};
			operation = methodName;
			
			traceMessages = new Array();
			//Create a new HTTP url service
			var urlRequest:URLRequest = new URLRequest();
			urlRequest.url = endpoint;
			urlRequest.contentType = "application/x-amf";

			var encodeTime:int = getTimer();
			var data:ByteArray = createAmfRequest(className, methodName, args);
			diagnostic.encodeTime = getTimer() - encodeTime;
			time = getTimer();
			diagnostic.sentSize = data.position;
			urlRequest.data = data;
			urlRequest.method = URLRequestMethod.POST;
			var h1:URLRequestHeader = new URLRequestHeader("Content-Type", "application/x-amf");
			//var h2:URLRequestHeader = new URLRequestHeader("Creditials", {userid:"danny", password:"test123"});
			//var h2 = new URLRequestHeader("Content-Length", data.length.toString());
			urlRequest.requestHeaders = [h1];
			loader.dataFormat = URLLoaderDataFormat.BINARY;
			loader.load(urlRequest);
			isRunning = true;
		}
		
		/**
		 * Kill the request
		 */
		public function kill():void
		{
			if(isRunning)
			{
				loader.close();
				dispatchEvent(new BrowserEvent(BrowserEvent.KILL_REQUEST));
				isRunning = false;
			}
		}

		/**
		 * Create a new AMF request
		 * 
		 * @see http://osflash.org/documentation/amf/envelopes/remoting
		 */
		private function createAmfRequest(className:String, methodName:String, args:Array):ByteArray
		{
			var data:ByteArray = new ByteArray();
			data.objectEncoding = encoding;					// Set the AMF encoding
			
			data.writeByte(0x00);
			data.writeByte(0x03);							// Set the Flash Player type (in this case, Flash Player 9+)
			
			 //Write the service browser header
			data.writeByte(0x00);							
			data.writeByte(0x01);							// Set the number of headers

			//Write the name
			data.writeByte(0x00);
			data.writeByte(14);
			data.writeUTFBytes("serviceBrowser");

			//Write required
			data.writeByte(0x00);

			//Write length
			data.writeByte(0x00);
			data.writeByte(0x00);
			data.writeByte(0x00);
			data.writeByte(0x02);

			//Write true
			data.writeByte(0x01);
			data.writeByte(0x01);

			//Write one body
			data.writeByte(0x00);
			data.writeByte(0x01);

			//Write the target (null)
			data.writeByte(0x00);
			data.writeByte(0x04);
			data.writeUTFBytes("null");

			//Now write the response handler
			data.writeByte(0x00);
			data.writeByte(0x02);
			data.writeUTFBytes("/1");

			//Write the number of bytes in the body
			data.writeByte(0x00);
			data.writeByte(0x00);
			data.writeByte(0x00);
			data.writeByte(0x00); //amfphp doesn't read this, so no matter

			var a:RemotingMessage = new RemotingMessage();
			a.clientId = "";
			a.messageId = "";
			a.operation = methodName;
			a.source = className;
			a.body = args;

			//Write the AMF3 bytecode
			if (encoding == ObjectEncoding.AMF3)
			{
				data.writeByte(0x11);
			}
			//Then the object
			data.writeObject([a]);

			return data;
		}

		/**
		 * Read and serialize the returned AMF data
		 */
		private function readData(event:Event):void
		{
			isRunning = false;
			diagnostic.pingTime = getTimer() - time;

			var rawData:ByteArray = loader.data;
			rawData.objectEncoding = ObjectEncoding.AMF3;

			//Determine if data is valid

			diagnostic.receivedSize = rawData.bytesAvailable;

			var decodeTime:int = getTimer();
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

					if (encoding == ObjectEncoding.AMF3)
					{
						var amf3Byte:uint = rawData.readUnsignedByte();
						rawData.objectEncoding = ObjectEncoding.AMF3;
					}
					
					var bodyVal:Object = rawData.readObject();
					rawData.objectEncoding = ObjectEncoding.AMF0;

					var sendEvent:AbstractEvent;
					if (target == '/1/onDebugEvents')
					{
						//Look at the bodyVal
						for (var j:uint = 0; j < bodyVal[0].length; j++)
						{
							if (bodyVal[0][j].EventType == 'trace')
							{
								//Bingo, we got trace
								traceMessages = bodyVal[0][j].messages;
							}
							else if (bodyVal[0][j].EventType == 'profiling')
							{
								//Bingo, we got trace
								profiling = bodyVal[0][j];
							}
						}
					}
					else if (target == '/1/onResult')
					{
						//We have a result event
						sendEvent = new ResultEvent(ResultEvent.RESULT, false, true, bodyVal.body);
							//dispatchEvent(re);
					}
					else if (target == '/1/onStatus')
					{
						//We have a fault event
						var fault:Fault = new Fault(bodyVal.faultCode, bodyVal.faultString, bodyVal.faultDetail);
						sendEvent = new FaultEvent(FaultEvent.FAULT, false, true, fault);
							//dispatchEvent(fe);
					}
				}
			}
			else
			{
				//Create a new Fault event
				rawData.position = 0;
				var errorMessage:String = rawData.readUTFBytes(rawData.length);
				var fault:Fault = new Fault("INVALID_AMF_MESSAGE", "Invalid AMF message", errorMessage);
				sendEvent = new FaultEvent(FaultEvent.FAULT, false, true, fault);
					//dispatchEvent(fe);
			}

			var totalTime:uint = 0;
			if (rawData.bytesAvailable == 2)
			{
				totalTime = rawData.readUnsignedShort();
			}
			else
			{
				totalTime = getTimer() - time;
				
				diagnostic.decodeTime = getTimer() - decodeTime;
				profiling.totalTime = totalTime;
				profiling.encodeTime = totalTime - (profiling.frameworkTime + profiling.decodeTime + profiling.includeTime + profiling.callTime);
			}
			
			dispatchEvent(sendEvent);
		}
	}
}