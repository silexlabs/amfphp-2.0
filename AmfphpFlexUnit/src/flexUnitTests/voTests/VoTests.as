package flexUnitTests.voTests
{
	import flash.net.ObjectEncoding;
	
	import flexUnitTests.TestConfig;
	
	import flexunit.framework.TestCase;
	
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.ObjEvent;

	public class VoTests extends TestCase
	{		
		private var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			_nc = new EnhancedNetConnection();
			_nc.connect(TestConfig.gateway);
			//_nc.objectEncoding = ObjectEncoding.AMF0;
			org.amfphp.test.Util.traceTestMeta(className, methodName);
			
		}
		
		
		public function testSendingVoWithArrays():void{
			_nc.callWithEvents("TestService.returnOneParam", new VoWithArrays());
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(sendingVoWithArraysResultHandler, 1000));
			
		}
		
		
		public function sendingVoWithArraysResultHandler(event:ObjEvent):void{
			assertTrue(event.obj is VoWithArrays);
		}
		
		public function testSendingNamespaceTestVo():void{
			_nc.callWithEvents("Sub1.NamespaceTestService.useVo", new NamespaceTestVo());
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(sendingNamespaceTestVoResultHandler, 1000));
			
		}
		
		
		public function sendingNamespaceTestVoResultHandler(event:ObjEvent):void{
			assertTrue(event.obj is NamespaceTestVo);
		}
		
	}
}