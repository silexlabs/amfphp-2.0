package flexUnitTests
{
	import flash.net.ObjectEncoding;
	
	import flexunit.framework.TestCase;
	
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.ObjEvent;

	public class Amf3TraitsTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;	
			
			_myConnection.destination = "amfphp2TestGateway"; 
			_myConnection.source = "MirrorService";			
		}
		
	
		/**
		 * note this currently fails because the deserializer doesn't parse the message properly
		 * */
		public function testSendingAndReceivingAnIExternalizable():void{
			_myConnection.returnOneParam(new ExternalizableDummy());
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingAnIExternalizableResultHandler, 1500));
			
		}
		
		
		public function sendingAndReceivingAnIExternalizableResultHandler(event:ResultEvent):void{
			assertTrue(event.result is ExternalizableDummy);
		}
	}
}