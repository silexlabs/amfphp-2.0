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
	
	public class SimpleServiceTest extends TestCase
	{
		private var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF0;
			_nc.connect("http://localhost:8888/workspaceNetbeans/amfphp-2.0/tests/testData/testGateway.php");			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		
		public function testReturnOneParam():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnOneParam, 500));
			_nc.simpleCall("MirrorService/returnOneParam", "testString");	
		}
		private function verifyReturnOneParam(event:DataEvent):void{
			assertEquals("testString", event.data);
		}
		
		public function testErrorFindingService():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 200));
			_nc.simpleCall("NoShirtNoShoesNoService/returnOneParam", "testString");	
		}
		
		
		public function testErrorFindingService2():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONSTATUS, addAsync(catchErrorFindingService, 200));
			_nc.simpleCall("nothingReally", "testString");	
		}
		
		private function catchErrorFindingService(event:DataEvent):void{
			assertTrue(event.data.indexOf("Exception") != -1);
		}
		

		
		
		
		
	}
}