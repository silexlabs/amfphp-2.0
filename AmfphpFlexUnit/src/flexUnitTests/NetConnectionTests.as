package flexUnitTests
{
	import flash.events.AsyncErrorEvent;
	import flash.events.DataEvent;
	import flash.events.Event;
	import flash.events.EventDispatcher;
	import flash.events.IOErrorEvent;
	import flash.events.NetStatusEvent;
	import flash.events.SecurityErrorEvent;
	import flash.net.NetConnection;
	import flash.net.ObjectEncoding;
	import flash.net.Responder;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.ObjEvent;
	
	public class NetConnectionTests extends TestCase
	{
		private var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.connect(TestConfig.gateway);
			org.amfphp.test.Util.traceTestMeta(className, methodName);
			
			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		public function testReturnOneParam():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnTestString, 1000));
			_nc.callWithEvents("TestService.returnOneParam", "testString");	
		}
		private function verifyReturnTestString(event:ObjEvent):void{
			assertTrue(event.obj is String);
			assertEquals("testString", event.obj);
		}
		
		public function testErrorFindingService():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 1000));
			_nc.callWithEvents("NoShirtNoShoesNoService/returnOneParam", "testString");	
		}
		
		
		public function testErrorFindingService2():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 1000));
			_nc.callWithEvents("nothingReally", "testString");	
		}
		
		private function catchErrorFindingService(event:ObjEvent):void{
			assertTrue(event.obj.faultString.indexOf("service not found") != -1);
		}
		
		
		//test can't be run, the netconnection doesn't accept the call
		public function fix_testSendingExternalizableObject():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnExternalizableDummy, 1000));
			var ext:ExternalizableDummy = new ExternalizableDummy();
			_nc.callWithEvents("TestService.returnOneParam", ext);	
		}			
		
		private function verifyReturnExternalizableDummy(event:ObjEvent):void{
			assertTrue(event.obj is ExternalizableDummy);
		}
		
		public function testDoubleCall():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyDoubleCall, 1000));
			_nc.callWithEvents("TestService.returnOneParam", "testString");	
			_nc.callWithEvents("TestService.returnOneParam", "testString");	
		}
		private function verifyDoubleCall(event:ObjEvent):void{
			assertTrue(event.obj is String);
			assertEquals("testString", event.obj);
		}
		
		/**
		 * discovery service is protected, so test fails. 
		 * @todo find a workaround to test it
		 * */
		public function fix_testDiscoveryService():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyDiscoveryService, 1000));
			_nc.callWithEvents("AmfphpDiscoveryService.discover");	
		}
		private function verifyDiscoveryService(event:ObjEvent):void{
			assertTrue(event.obj.AmfphpDiscoveryService.methods.discover.parameters.length == 0);
		}
		
				
		public function testNamespaceServiceCall():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyNamespaceServiceCall, 1000));
			_nc.callWithEvents("Sub1/NamespaceTestService/bla");	
		}
		private function verifyNamespaceServiceCall(event:ObjEvent):void{
			assertTrue(event.obj is String);
			assertEquals("bla", event.obj);
		}
		

		
		
		
		
	}
}