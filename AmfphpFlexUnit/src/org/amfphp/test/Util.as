package org.amfphp.test
{
	public class Util
	{
		public static function traceTestMeta(testClassName:String, testMethodName:String):void{
			//hackish, todo
			var testName:String = testClassName.replace('flexUnitTests::', '').replace('flexUnitTests.voTests::', '') + "_" + testMethodName;
			trace(testName + "_request.amf");
			trace(testName + "_request2.amf");
			trace(testName + "_expectedResponse.amf");
			trace(testName + "_expectedResponse2.amf");
			trace("test('" + testName + "');");
			
		}
		
		
	}
}