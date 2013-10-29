package flexUnitTests.voTests
{
	import flash.net.registerClassAlias;

	public class NamespaceTestVo
	{
		public var dummyData:String = "client dummy data";
		
		public function NamespaceTestVo()
		{
		}
		
		static public function register():void {
			registerClassAlias("Sub1.NamespaceTestVo", NamespaceTestVo);
		}
	}
}