package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.utils.ByteArray;
	import flash.xml.XMLDocument;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	/**
	 * adds amf3 specific tests to the tests that work for both amf0 and am3
	 * */
	public class ReferenceAmf3Tests extends ReferenceCommonTestsDisable
	{		
		
		[Before]
		override public function setUp():void
		{
			super.setUp();
			_nc.objectEncoding = ObjectEncoding.AMF3;
			//this second connect call is important because of a flash bug: 
			//As the setting to AMF3 is done after the connect in super.setup, the actual data sent is in amf3 without this workaround
			_nc.connect(TestConfig.gateway);
			org.amfphp.test.Util.traceTestMeta(className, methodName);
			
		}
		
		/**
		 * send a reference of byte arrays containing a boolean set to false
		 * flash somehow can't send this. what's wrong here?
		 * */
		public function fix_testByteArrayReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnByteArray, 1000));
			var testByteArray:ByteArray = new ByteArray();
			testByteArray.writeBoolean(false);
			_nc.callWithEvents("TestService.returnOneParam", new Array[testByteArray, testByteArray]);	
			
		}
		
		private function verifyReturnByteArray(event:ObjEvent):void{
			assertTrue(event.obj is ByteArray);
			var retByteArray:ByteArray = event.obj as ByteArray;
			assertEquals(false, retByteArray.readBoolean());
		}
		
		/**
		 * send an xml object (the as3 E4X object, or the as2 XML object)
		 * the as3 netconnection trips up on this when using AMF0...
		 * flash somehow can't send this. what's wrong here?
		 * */
		public function fix_testXmlReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnXml, 1000));
			var testVar:XML = new XML("<root>bla</root>");
			_nc.callWithEvents("TestService.returnOneParam", new Array[testVar, testVar]);	
			
		}
		
		private function verifyReturnXml(event:ObjEvent):void{
			assertTrue(event.obj is XML);
		}		
		
		
		
	}
}