package org.amfphp.test
{
	import flash.net.registerClassAlias;
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	import flash.utils.IExternalizable;
	
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
		}

	}
}