package flexUnitTests.voTests {
	import flash.net.registerClassAlias;

	public class VoWithArrays {

		public var test1_arr:Array = new Array();
		public var test2_arr:Array = new Array();
		
		static public function register():void {
			registerClassAlias("ItemVO", VoWithArrays);
		}
		
	}
}
