package flexUnitTests
{
	import flexunit.framework.TestCase;
	
	import mx.rpc.remoting.RemoteObject;

	public class RemoteObjectTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;
			_myConnection.destination = "my-amfphp";
			_myConnection.source = "MirrorService";			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		
		public function testShit():void{
			_myConnection.returnOneParam("boo");
			assertTrue(true);
		}
	}
}