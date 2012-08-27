package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.utils.ByteArray;
	import flash.utils.describeType;
	import flash.xml.XMLDocument;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	/**
	 * adds amf3 specific tests to the tests that work for both amf0 and am3
	 * */
	public class Amf3Tests extends AmfCommonTests
	{		
		
		[Before]
		override public function setUp():void
		{
			super.setUp();
			_nc.objectEncoding = ObjectEncoding.AMF3;
			//this second connect call is important because of a flash bug: 
			//As the setting to AMF3 is done after the connect in super.setup, the actual data sent is in amf3 without this workaround
			_nc.connect(TestConfig.NC_GATEWAY_URL);
			
			
		}
		
		/**
		 * send a byte array containing a boolean set to false
		 * */
		public function testByteArray():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnByteArray, 1000));
			var testByteArray:ByteArray = new ByteArray();
			testByteArray.writeBoolean(false);
			_nc.callWithEvents("TestService.returnOneParam", testByteArray);	
			
		}
		
		private function verifyReturnByteArray(event:ObjEvent):void{
			assertTrue(event.obj is ByteArray);
			var retByteArray:ByteArray = event.obj as ByteArray;
			assertEquals(false, retByteArray.readBoolean());
		}
		
		/**
		 * send an xml object (the as3 E4X object, or the as2 XML object)
		 * the as3 netconnection trips up on this when using AMF0...
		 * */
		public function testXml():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnXml, 1000));
			var testVar:XML = new XML("<root>bla</root>");
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		private function verifyReturnXml(event:ObjEvent):void{
			assertTrue(event.obj is XML);
		}		
		
		
		
	}
}