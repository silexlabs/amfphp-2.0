package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	public class CallButton extends Sprite
	{
		
		public function CallButton()
		{
			super();
			graphics.beginFill(0, 0.5);
			graphics.drawRect(0, 0, 40, 15);
			var textField:TextField = new TextField();
			addChild(textField);
			textField.text = "call";
			textField.width = 40;
			textField.height = 15;
			textField.autoSize = TextFieldAutoSize.CENTER;
			useHandCursor = buttonMode = true;
			mouseChildren = false;
		
			
		}
	}
}