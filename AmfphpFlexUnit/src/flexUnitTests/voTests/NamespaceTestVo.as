package flexUnitTests.voTests
{
	import flash.net.registerClassAlias;

	public class NamespaceTestVo
	{
		public var dummyData:String = "client dummy data";
		
		private static const REGISTERED:* = registerClassAlias("Sub1.NamespaceTestVo", NamespaceTestVo);
		
		
	}
}