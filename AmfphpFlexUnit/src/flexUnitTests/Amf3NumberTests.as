package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.xml.XMLDocument;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	public class Amf3NumberTests extends TestCase
	{		
		
		protected var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF3;
			_nc.connect(TestConfig.NC_GATEWAY_URL);
			
		}
		
		public function testMinusOne():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnMinusOne, 1000));
			var testVar:int = -1;
			_nc.callWithEvents("TestService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnMinusOne(event:ObjEvent):void{
			assertTrue(event.obj is int);
			assertEquals(-1, event.obj);
		}
		
		public function test2Power30():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturn2Power30, 1000));
			var testVar:Number = 2^30;
			_nc.callWithEvents("TestService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturn2Power30(event:ObjEvent):void{
			assertTrue(event.obj is Number);
			assertEquals(2^30, event.obj);
		}	
		
	}
}