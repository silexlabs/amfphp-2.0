package flexUnitTests
{
	import flash.net.ObjectEncoding;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;

	public class HeaderTests extends TestCase
	{		
		
		protected var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF0;
			_nc.connect(TestConfig.gateway);
			org.amfphp.test.Util.traceTestMeta(className, methodName);
			
			
		}
		
		public function testServiceReadsHeader():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyTestServiceReadsHeader, 1000));
			_nc.addHeader("test", false, "testdata");
			_nc.callWithEvents("TestService.returnTestHeader");	
			
		}
		
		private function verifyTestServiceReadsHeader(event:ObjEvent):void{
			assertEquals("testdata", event.obj);
		}
		
		
	}
}