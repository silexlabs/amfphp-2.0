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
			_myConnection.source = "TestService";			
		}
		
		
		/**
		 * this works properly but somehow flex unit trips up on it. Charles Proxy fails to parse IExternalizable.  
		 * */
		public function testSendingAndReceivingAnIExternalizable():void{
			_myConnection.returnOneParam(new ExternalizableDummy());
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingAnIExternalizableResultHandler, 3000));
			
		}
		
		
		public function sendingAndReceivingAnIExternalizableResultHandler(event:ResultEvent):void{
			assertTrue(event.result is ExternalizableDummy);
		}
	}
}