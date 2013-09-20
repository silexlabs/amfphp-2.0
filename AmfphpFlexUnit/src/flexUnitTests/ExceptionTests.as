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
			_nc.connect(TestConfig.gateway);
			org.amfphp.test.Util.traceTestMeta(className, methodName);

			
		}
		
		public function testException():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(verifyTestExceptionStatus, 1000));
			var testVar:int = 1;
			_nc.callWithEvents("TestService.throwException", testVar);	
			
		}
		
		private function verifyTestExceptionStatus(event:ObjEvent):void{
			assertEquals(123,event.obj.faultCode);
			assertEquals("test exception 1", event.obj.faultString);
		}
		
		
		public function testWrongNumberOfArguments():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(verifyWrongNumberOfArgumentsStatus, 1000));
			var testVar:int = -1;
			_nc.callWithEvents("TestService.returnSum", testVar);	
			
		}
		
		private function verifyWrongNumberOfArgumentsStatus(event:ObjEvent):void{
			assertEquals("Invalid number of parameters for method returnSum in service TestService : 2  required, 2 total, 1 provided", event.obj.faultString);
		}		
	}
}