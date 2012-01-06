package flexUnitTests
{
	import flexunit.framework.TestCase;
	
	import mx.collections.ArrayCollection;
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.TestCustomClass1;

	public class RemoteObjectTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;	

			_myConnection.destination = "amfphp2TestGateway"; 
			_myConnection.source = "TestService";			
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
			test.addItem("bla");
			_myConnection.returnOneParam(test);
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingArrayCollectionResultHandler, 1000));
			
		}
		
		/**
		 * this fails, as sending an externalized object from amfphp is not yet supported(an array collection is externalized, 
		 * this is a special amf trait. see spec for details
		 * */
		public function sendingAndReceivingArrayCollectionResultHandler(event:ResultEvent):void{
			assertTrue(event.result is ArrayCollection);
			assertEquals(1, event.result.length);
			assertEquals("bla", event.result.getItemAt(0));
		}
		
		
	}
}