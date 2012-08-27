package flexUnitTests
{
	import flash.events.Event;
	import flash.net.FileReference;
	import flash.net.ObjectEncoding;
	import flash.net.URLLoader;
	import flash.net.URLLoaderDataFormat;
	import flash.net.URLRequest;
	import flash.utils.ByteArray;
	
	import flexunit.framework.TestCase;
	
	import mx.rpc.events.FaultEvent;
	import mx.rpc.events.ResultEvent;
	import mx.rpc.remoting.RemoteObject;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ExternalizableDummy;
	import org.amfphp.test.ObjEvent;

	public class ByteArrayTests extends TestCase
	{		
		private var _myConnection:RemoteObject;		
		private var _urlLoader:URLLoader;
		
		[Before]
		override public function setUp():void
		{
			_myConnection = new RemoteObject;	
			
			_myConnection.destination = "bla"; 
			_myConnection.endpoint = TestConfig.NC_GATEWAY_URL;
			_myConnection.source = "ByteArrayTestService";
			_urlLoader = new URLLoader();
			_urlLoader.dataFormat = URLLoaderDataFormat.BINARY;
			_urlLoader.addEventListener(Event.COMPLETE, onComplete);
		}

		private function onComplete(event:Event):void
		{
			var data:ByteArray = _urlLoader.data; 
			data.compress();
			_myConnection.uploadCompressedByteArray(data);
			
			_myConnection.addEventListener(ResultEvent.RESULT, addAsync(sendingAndReceivingACompressedImageResultHandler, 3000));
			
		}

		
		public function testSendingAndReceivingACompressedImage():void{
			_urlLoader.load(new URLRequest("cc-logo.jpg"));
		}

		
		
		public function sendingAndReceivingACompressedImageResultHandler(event:ResultEvent):void{
			assertTrue(event.result);
		}
	}
}