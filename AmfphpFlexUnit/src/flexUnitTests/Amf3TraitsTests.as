package flexUnitTests
{
	import flash.net.ObjectEncoding;
	
	import flexUnitTests.voTests.VoWithArrays;
	
	import flexunit.framework.TestCase;
	
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.ObjEvent;

	public class Amf3TraitsTests extends TestCase
	{		
		private var _nc:EnhancedNetConnection;	
		
		[Before]
		override public function setUp():void
		{
			_nc = new EnhancedNetConnection();
			_nc.connect(TestConfig.gateway);	
			org.amfphp.test.Util.traceTestMeta(className, methodName);
			
		}
		
		
		/**
		 * This fails as right now the AMF Serializer doesn't support writing externalizable data,
		 * so an ExternalizableDummy is created, but its "readExternal" method is never called
		 *  
		 * */
		public function fix_testSendingAndReceivingAnIExternalizable():void{
			_nc.callWithEvents("TestService.returnOneParam",new ExternalizableDummy());
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(sendingAndReceivingAnIExternalizableResultHandler, 3000));
			
		}
		
		
		public function sendingAndReceivingAnIExternalizableResultHandler(event:ObjEvent):void{
			assertTrue(event.obj is ExternalizableDummy);
			assertEquals(1234, event.obj.getOne());
		}
		
		/**
		 * the aim of this test is to make sure traits references are properly tracked.
		 * An anonymous object has traits, and this must be accounted for. 
		 * So The trait reference for the 2nd VO is actually 1, not 0
		 * note: the 2 vos must be different, otherwise the second can be sent as a reference to the first
		 * */
		public function testSendingMixOfAnonymousAndTypedObjects():void{
			var vo1:VoWithArrays = new VoWithArrays();
			vo1.test1_arr = new Array("bla");
			var anon:Object = new Object();
			anon.data = [vo1, new VoWithArrays()];
			_nc.callWithEvents("TestService.returnOneParam", anon);
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(sendingMixOfAnonymousAndTypedObjectsResultHandler, 3000));
			
		}
		
		public function sendingMixOfAnonymousAndTypedObjectsResultHandler(event:ObjEvent):void{
			assertTrue(event.obj.data[0] is VoWithArrays);
		}
	}
}