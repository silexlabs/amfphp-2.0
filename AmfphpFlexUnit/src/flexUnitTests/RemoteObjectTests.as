package flexUnitTests
{
	import flexunit.framework.TestCase;
	
	import mx.collections.ArrayCollection;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.TestCustomClass1;
	import org.amfphp.test.Util;

	public class RemoteObjectTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;	

			_myConnection.destination = "amfphp2TestGateway"; 
			_myConnection.endpoint = TestConfig.gateway;
			_myConnection.source = "TestService";	
			Util.traceTestMeta(className, methodName);

		}
		
		[After]
		override public function tearDown():void
		{
		}
		
		public function testSimpleRequest():void{
			//_myConnection.getServices();
			_myConnection.returnOneParam("boo");
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(simpleRequestResultHandler, 1000));
		}
		
		public function simpleRequestResultHandler(event:ResultEvent):void{
			assertEquals("boo", event.result);
		}
		
		public function testBadRequest():void{
			_myConnection.throwException();
			_myConnection.addEventListener(FaultEvent.FAULT, addAsync(badRequestFaultHandler, 1000));
		}
		
		public function badRequestFaultHandler(event:FaultEvent):void{
			assertTrue(true);
		}
		
		public function testSendingAndReceivingATypedObject():void{
			_myConnection.returnOneParam(new TestCustomClass1());
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingATypedObjectResultHandler, 1000));
			
		}
		
		public function sendingAndReceivingATypedObjectResultHandler(event:ResultEvent):void{
			assertTrue(event.result is TestCustomClass1);
		}
		
		public function testSendingAndReceivingArrayCollection():void{
			var test:ArrayCollection = new ArrayCollection();
			test.addItem("bla1");
			test.addItem("bla2");
			_myConnection.testArrayCollection(test);
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingArrayCollectionResultHandler, 1000));
			
		}
		
		public function sendingAndReceivingArrayCollectionResultHandler(event:ResultEvent):void{
			assertTrue(event.result is ArrayCollection);
			assertEquals("bla1", event.result.getItemAt(0));
			assertEquals("bla2", event.result.getItemAt(1));
			
		}
		
		
	}
}