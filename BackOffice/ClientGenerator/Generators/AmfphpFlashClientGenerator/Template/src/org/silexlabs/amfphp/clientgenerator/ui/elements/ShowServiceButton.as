package org.silexlabs.amfphp.clientgenerator.ui.elements
{
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	
	public class ShowServiceButton extends Sprite
	{
		private var _label:String;
		
		private var _textField:TextField;
		public function ShowServiceButton()
		{
			super();
			graphics.beginFill(0, 0.5);
			graphics.drawRect(0, 0, 100, 15);
			_textField = new TextField();
			addChild(_textField);
			_textField.width = 100;
			_textField.height = 15;
			_textField.autoSize = TextFieldAutoSize.CENTER;
			useHandCursor = buttonMode = true;
			mouseChildren = false;
			
		}
		
		public function get label():String{
			return _label;
		}
		
		public function set label(value:String):void{
			_label = value;
			_textField.text = value;
		}
	}
}