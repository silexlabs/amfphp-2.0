package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.utils.ByteArray;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	public class Amf3TypeTests extends Amf0TypeTests
	{		
		
		[Before]
		override public function setUp():void
		{
			super.setUp();
			_nc.objectEncoding = ObjectEncoding.AMF3;
			
		}
		
		/**
		 * send a byte array containing a boolean set to false
		 * */
		public function testByteArray():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnByteArray, 1000));
			var testByteArray:ByteArray = new ByteArray();
			testByteArray.writeBoolean(false);
			_nc.simpleCall("MirrorService/returnOneParam", testByteArray);	
			
		}
		
		private function verifyReturnByteArray(event:ObjEvent):void{
			assertTrue(event.obj is ByteArray);
			var retByteArray:ByteArray = event.obj as ByteArray;
			assertEquals(false, retByteArray.readBoolean());
		}
		
		
		
	}
}