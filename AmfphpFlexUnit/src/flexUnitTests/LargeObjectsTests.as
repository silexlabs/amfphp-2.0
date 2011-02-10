package flexUnitTests
{
	import flexunit.framework.TestCase;
	
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	import mx.utils.ObjectUtil;
	import mx.utils.UIDUtil;

	public class LargeObjectsTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		
		private var _manyUniqueStrings:Array;
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;
			_myConnection.destination = "amfphp1.9";
			_myConnection.destination = "amfphp2"; 
			_myConnection.destination = "remoteamfphp1.9"; 
			_myConnection.destination = "remoteamfphp2"; 
			_myConnection.source = "MirrorService";			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		
		public function testManyUniqueStrings():void{
			_manyUniqueStrings = new Array();
			for(var i:int = 0; i < 10000; i++){
				var uid:String = UIDUtil.createUID();
				_manyUniqueStrings.push(uid);
			}
			_myConnection.returnOneParam(_manyUniqueStrings);
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(manyUniqueStringsResultHandler, 10000));
			
		}
		
		public function manyUniqueStringsResultHandler(event:ResultEvent):void{
			assertEquals(ObjectUtil.toString(_manyUniqueStrings), ObjectUtil.toString(event.result));
		}
		
		
	}
}