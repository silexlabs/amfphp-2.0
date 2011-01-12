package org.amfphp.test
{
	import flash.utils.IDataInput;
	import flash.utils.IDataOutput;
	import flash.utils.IExternalizable;

	public class ExternalizableDummy implements IExternalizable {
		
		private var one:int;
		private var two:int;
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