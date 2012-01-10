package org.amfphp.test
{
	import flash.net.registerClassAlias;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	import flash.utils.IExternalizable;
	
	/**
	 * must have an explicit registered type because somehow otherwise netconnection fails to encode it.
	 * Other than that note the writeExternal method sets 'one' to 1234. This is to make sure the method is called, as the same data is otherwise sent and received. 
	 * */
	[RemoteClass(alias="ExternalizableDummy")]
	public class ExternalizableDummy implements IExternalizable {
		
		private var one:int = 1;
		private var two:int = 2;
		
		public function ExternalizableDummy(){
			registerClassAlias("ExternalizableDummy", ExternalizableDummy);
		}
		
		public function writeExternal(output:IDataOutput):void {
			output.writeInt(one);
			output.writeInt(two);
		}
		public function readExternal(input:IDataInput):void {
			one = input.readInt();
			two = input.readInt();
			one = 1234;
		}
		
		public function getOne():int{
			return one;
		}

	}
}