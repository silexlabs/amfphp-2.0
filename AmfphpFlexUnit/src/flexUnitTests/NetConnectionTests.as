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
			//_nc.objectEncoding = ObjectEncoding.Amf0;
			_nc.connect(TestConfig.NC_GATEWAY_URL);
			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		public function testReturnOneParam():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnOneParam, 500));
			_nc.simpleCall("MirrorService/returnOneParam", "testString");	
		}
		private function verifyReturnOneParam(event:ObjEvent):void{
			assertEquals("testString", event.obj);
		}
		
		public function testErrorFindingService():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 200));
			_nc.simpleCall("NoShirtNoShoesNoService/returnOneParam", "testString");	
		}
		
		
		public function testErrorFindingService2():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 200));
			_nc.simpleCall("nothingReally", "testString");	
		}
		
		private function catchErrorFindingService(event:ObjEvent):void{
			assertTrue(event.obj.faultString.indexOf("service not found") != -1);
		}
		
		
		//test can't be run, the netconnection doesn't accept the call
		/*
		public function testSendingExternalizableObject():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 200));
			var ext:ExternalizableDummy = new ExternalizableDummy();
			_nc.simpleCall("MirrorService/returnOneParam", ext);	
		}			
		*/
		

		
		
		
		
	}
}