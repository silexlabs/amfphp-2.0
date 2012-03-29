package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFieldType;
	
	public class ResultDisplay extends TextField
	{
		public function ResultDisplay()
		{
			super();
			type = TextFieldType.DYNAMIC;
			border = true;
			width = 500;
			height = 200;
			multiline = wordWrap = true;
			
		}
	}
}