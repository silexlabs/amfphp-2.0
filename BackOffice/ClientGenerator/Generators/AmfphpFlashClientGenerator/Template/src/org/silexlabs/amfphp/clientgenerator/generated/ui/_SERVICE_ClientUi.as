package org.silexlabs.amfphp.clientgenerator.generated.ui
{
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.net.NetConnection;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFieldType;
	
	import org.silexlabs.amfphp.clientgenerator.NetConnectionSingleton;
	import org.silexlabs.amfphp.clientgenerator.generated.service._SERVICE_Client;
	import org.silexlabs.amfphp.clientgenerator.ui.elements.CallButton;
	import org.silexlabs.amfphp.clientgenerator.ui.elements.ResultDisplay;
	import org.silexlabs.amfphp.clientgenerator.ui.elements.StringInput;
	
	public class _SERVICE_ClientUi extends MovieClip
	{
		private var _service:_SERVICE_Client;
		
		/*ACG_METHOD*/
		//_METHOD_ inputs /*ACG_PARAMETER*/
		public var _METHOD___PARAMETER__Input:TextField; /*ACG_PARAMETER*/
		public var _METHOD__callButton:Sprite;		/*ACG_METHOD*/
		public var returnSum_CallButton:Sprite;		
		public var resultDisplay:TextField;
		public function _SERVICE_ClientUi()
		{
			super();
			var xLayoutMultiplier:int = 0;
			var yLayoutMultiplier:int = 0;
			var methodLabel:TextField;
			
			_service = new _SERVICE_Client(NetConnectionSingleton.getNetConnection());
			graphics.beginFill(0, 0.3);
			/*ACG_METHOD*/
			//_METHOD_ ui
			xLayoutMultiplier = 0;
			methodLabel = new TextField();
			methodLabel.autoSize = TextFieldAutoSize.LEFT;
			methodLabel.y = 40 * yLayoutMultiplier;
			methodLabel.text = '_METHOD_';
			addChild(methodLabel);
			graphics.drawRect(0, 35 + yLayoutMultiplier * 40, 500, 1);
			/*ACG_PARAMETER*/
			if(!_METHOD___PARAMETER__Input){
				_METHOD___PARAMETER__Input = new StringInput();
				_METHOD___PARAMETER__Input.x = 100 * xLayoutMultiplier;
				_METHOD___PARAMETER__Input.y = 15  + 40 * yLayoutMultiplier;
				addChild(_METHOD___PARAMETER__Input);
			}
			xLayoutMultiplier++;
			/*ACG_PARAMETER*/
			
			if(!_METHOD__callButton){
				_METHOD__callButton = new CallButton();
				_METHOD__callButton.x = 400;
				_METHOD__callButton.y = 15 + 40 * yLayoutMultiplier;
				addChild(_METHOD__callButton);
			}
			_METHOD__callButton.addEventListener(MouseEvent.CLICK, _METHOD__callButtonClickHandler);
			yLayoutMultiplier++;
			/*ACG_METHOD*/
			if(!resultDisplay){
				resultDisplay = new ResultDisplay();
				resultDisplay.y = 40 * yLayoutMultiplier;
				addChild(resultDisplay);
			}
			
		}
		
		/*ACG_METHOD*/
		private function _METHOD__callButtonClickHandler(event:MouseEvent):void{
			_service._METHOD_(/*ACG_PARAMETER_COMMA*/_METHOD___PARAMETER__Input.text/*ACG_PARAMETER_COMMA*/).setResultHandler(_METHOD_ResultHandler).setErrorHandler(errorHandler); 
		}
		
		private function _METHOD_ResultHandler(obj:Object):void{
			resultDisplay.appendText("result : " + obj + "\n");
		}
		/*ACG_METHOD*/
		
		private function errorHandler(obj:Object):void{
			resultDisplay.appendText("error : " + obj.faultString + "\n");
		}
	}
}