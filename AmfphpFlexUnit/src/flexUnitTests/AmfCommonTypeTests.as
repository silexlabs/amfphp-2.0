package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.utils.ByteArray;
	import flash.xml.XMLDocument;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;
	
	/**
	 * test sending all basic object types from the AMF0 and making sure the returned data is the same.
	 * These tests run using AMF0, but they can also be used for AM3, just override the setup method
	 * note: references are tricky, as they can only be indirectly observed, and there is no "object end" test case
	 * */
	public class AmfCommonTypeTests extends TestCase
	{		
		
		protected var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF0;
			_nc.connect(TestConfig.NC_GATEWAY_URL);
			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		
		/**
		 * send a Number (0.15)
		 * */
		public function testNumber():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnNumber, 1000));
			var testVar:Number = 0.15;
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnNumber(event:ObjEvent):void{
			assertTrue(event.obj as Number == 0.15);
		}
		
		/**
		 * send a Boolean (false)
		 * */
		public function testFalse():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnFalse, 1000));
			var testVar:Boolean = false;
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnFalse(event:ObjEvent):void{
			assertTrue(event.obj is Boolean);
			assertTrue(event.obj as Boolean == false);
		}
		
		/**
		 * send a Boolean (true)
		 * */
		public function testTrue():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnTrue, 1000));
			var testVar:Boolean = true;
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnTrue(event:ObjEvent):void{
			assertTrue(event.obj is Boolean);
			assertTrue(event.obj as Boolean == true);
		}
		
		/**
		 * send a String ("hi")
		 * */
		public function testString():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnString, 1000));
			var testVar:String = "hi";
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnString(event:ObjEvent):void{
			assertTrue(event.obj is String);
			assertTrue(event.obj as String == "hi");
		}
		
		/**
		 * send a Object (empty)
		 * */
		public function testObject():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnObject, 1000));
			var testVar:Object = new Object();
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnObject(event:ObjEvent):void{
			assertTrue(event.obj is Object);
		}
		
		/**
		 * send a Null (false)
		 * */
		public function testNull():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnNull, 1000));
			var testVar:Object = null;
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnNull(event:ObjEvent):void{
			assertTrue(event.obj == null);
		}
		
		
		/**
		 * send an undefined. This test fails because netconnection converts undefined to null when sending
		 * rename to fix!
		 * */
		public function arghTestUndefined():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnUndefined, 1000));
			var testVar:*;
			trace("testUndefined " + testVar);
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnUndefined(event:ObjEvent):void{
			//TODO
			assertFalse(true);
			assertTrue(event.obj as Number == undefined);
		}
		
				
		/**
		 * send something with a reference ([["bla"], ["bla"]], really, but using a reference to a ["bla"] array)
		 *  @todo not really sure to provoke the writing of an array with references in php.
		 * */
		public function testReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnReference, 1000));
			var refferedArray:Array = ["bla"];
			var testVar:Array = [refferedArray, refferedArray];
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnReference(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			assertTrue(event.obj[0] == "bla");
			assertTrue(event.obj[1] == "bla");
		}
		
		/**
		 * send a EcmaArray ({testName:"testVal"})
		 * */
		public function testEcmaArray():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnEcmaArray, 1000));
			var testVar:Object = {testName:"testVal"};
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnEcmaArray(event:ObjEvent):void{
			assertTrue(event.obj.testName == "testVal");
		}
		
		/**
		 * send a Strict Array (["first", "second"])
		 * */
		public function testStrictArray():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnStrictArray, 1000));
			var testVar:Array = ["first", "second"];
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnStrictArray(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			assertTrue(event.obj[0] == "first");
			assertTrue(event.obj[1] == "second");
		}
		
		/**
		 * send a Date (now)
		 * */
		public function testDate():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnDate, 1000));
			var testVar:Date = new Date();
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnDate(event:ObjEvent):void{
			assertTrue(event.obj is Date);
		}
		
		
		/**
		 * send an xml document object (comparable to as2 xml object)
		 * */
		public function testXMLDocument():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyReturnXMLDocument, 1000));
			var testVar:XMLDocument = new XMLDocument("<root>bla</root>");
			_nc.simpleCall("MirrorService/returnOneParam", testVar);	
			
		}
		
		private function verifyReturnXMLDocument(event:ObjEvent):void{
			assertTrue(event.obj is XMLDocument);
		}		
	
		
		
	}
}