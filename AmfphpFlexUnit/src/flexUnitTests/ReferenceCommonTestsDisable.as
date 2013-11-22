package flexUnitTests
{
	import flash.net.ObjectEncoding;
	import flash.utils.ByteArray;
	import flash.xml.XMLDocument;
	
	import flexUnitTests.voTests.VoWithArrays;
	
	import flexunit.framework.TestCase;
	
	import org.amfphp.test.EnhancedNetConnection;
	import org.amfphp.test.ObjEvent;
	
	/**
	 * test sending stock messages in AMF0. for AM3, just override the setup method
	 * note: references are tricky, as they can only be indirectly observed
	 * 
	 * note: in AMF0, it seems flash implementation is bugged. Sending [["bla"], ["bla"]](the second one being a reference to the first)
	 *  results in a looped reference. The referred array is sent with reference '1' whereas it should be '2'
	 * In Charles this shows up as a circular reference, as it means the main array contains itself.
	 * This is because the parameters to the call is an array itself. IE what is being decoded is [ [["bla"], ["bla"]] ]
	 * Unoortnuately this means a lot of tests fail with AMF0.
	 * 
	 * named xxxDisable to avoid being picked up by ant testrunner generation
	 * */
	public class ReferenceCommonTestsDisable extends TestCase
	{		
		
		protected var _nc:EnhancedNetConnection;
		
		[Before]
		override public function setUp():void
		{
			
			_nc = new EnhancedNetConnection();
			_nc.objectEncoding = ObjectEncoding.AMF0;
			_nc.connect(TestConfig.gateway);
			org.amfphp.test.Util.traceTestMeta(className, methodName);

			
		}
		
		[After]
		override public function tearDown():void
		{
		}
		

				
		/**
		 * send something with a reference ([["bla"], ["bla"]], really, but using a reference to a ["bla"] array)
		 *  @todo not really sure to provoke the writing of an array with references in php.
		 * 
		 * */
		public function testArrayReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyArrayReturnReference, 1000));
			var refferedArray:Array = ["bla"];
			var testVar:Array = [refferedArray, refferedArray];
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		/**
		 * this only checks that the data is valid. Array references in the serailizer are disabled
		 * */
		private function verifyArrayReturnReference(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			assertEquals("bla", event.obj[0]);
			assertEquals("bla", event.obj[1]);
		}
		
		public function testObjectReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyObjectReturnReference, 1000));
			var reffered:* = {bla:"bla"};
			var testVar:Array = [reffered, reffered];
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		/**
		 * this only checks that the data is valid.
		 * */
		private function verifyObjectReturnReference(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			assertEquals("bla", event.obj[0].bla);
			assertEquals("bla", event.obj[1].bla);
		}
		
		public function testSecondObjectReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifySecondObjectReturnReference, 1000));
			var reffered:* = {bla:"bla"};
			var reffered2:* = {bla2:"bla2"};
			var testVar:Array = [reffered, reffered, {}, ["qwe"], reffered2, reffered2];
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		/**
		 * this only checks that the data is valid.
		 * */
		private function verifySecondObjectReturnReference(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			assertEquals("bla", event.obj[0].bla);
			assertEquals("bla", event.obj[1].bla);
			assertEquals("bla2", event.obj[4].bla2);
			assertEquals("bla2", event.obj[5].bla2);
		}
		
		
		public function testTypedObjectReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyTypedObjectReturnReference, 1000));
			var reffered:* = new VoWithArrays();
			var testVar:Array = [reffered, reffered];
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		/**
		 * this only checks that the data is valid.
		 * */
		private function verifyTypedObjectReturnReference(event:ObjEvent):void{
			assertTrue(event.obj[0] is VoWithArrays);
			assertTrue(event.obj[1] is VoWithArrays);
			
		}	
		
		/**
		 * test what happens beyond MAX_STORE_SIZE(1024)
		 * */
		public function testManyObjectsReference():void{
			_nc.addEventListener(EnhancedNetConnection.EVENT_ONRESULT, addAsync(verifyManyObjectsReturnReference, 5000));
			var testVar:Array = new Array();
			for(var i:int = 0; i < 600;i++){
				var reffered:VoWithArrays = new VoWithArrays();
				reffered.test1_arr.push(i);
				testVar.push(reffered);					
				testVar.push(reffered);					
			}
			_nc.callWithEvents("TestService.returnOneParam", testVar);	
			
		}
		
		/**
		 * this only checks that the data is valid.
		 * */
		private function verifyManyObjectsReturnReference(event:ObjEvent):void{
			assertTrue(event.obj is Array);
			//test in space where references are made
			assertEquals(300, event.obj[600].test1_arr[0]);
			//test beyond
			assertEquals(599, event.obj[1199].test1_arr[0]);
		}
		
		
	}
}