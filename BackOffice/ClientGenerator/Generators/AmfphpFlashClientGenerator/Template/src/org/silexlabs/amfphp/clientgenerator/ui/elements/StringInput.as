package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFieldType;
	
	public class StringInput extends TextField
	{
		public function StringInput()
		{
			super();
			type = TextFieldType.INPUT;
			border = true;
			width = 80;
			height = 15;
			
		}
	}
}