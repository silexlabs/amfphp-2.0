package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.xml.XMLDocument;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	public class ExceptionTests extends TestCase
	{		
		
		protected var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF3;
			_nc.connect(TestConfig.NC_GATEWAY_URL);
			
		}
		
		public function testException():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(verifyStatus, 1000));
			var testVar:int = -1;
			_nc.simpleCall("TestService/throwException", testVar);	
			
		}
		
		private function verifyStatus(event:ObjEvent):void{
			assertEquals(123,event.obj.faultCode);
			assertEquals("test exception", event.obj.faultString);
		}
	}
}