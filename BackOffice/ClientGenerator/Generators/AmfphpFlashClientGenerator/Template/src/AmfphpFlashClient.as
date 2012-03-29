package
{
	/*ACG_SERVICE*/
	import org.silexlabs.amfphp.clientgenerator.generated.ui._SERVICE_ClientUi;	/*ACG_SERVICE*/
	import org.silexlabs.amfphp.clientgenerator.ui.elements.ShowServiceButton;
	
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.events.MouseEvent;
	import flash.net.NetConnection;
	
	public class AmfphpFlashClient extends MovieClip
	{
		private var _serviceUi:Sprite;
		
		/*ACG_SERVICE*/
		public var show_SERVICE_UiBtn:ShowServiceButton;
		/*ACG_SERVICE*/
		
		public function AmfphpFlashClient()
		{
			var layoutMultiplier:int = 0;
			
			/*ACG_SERVICE*/
			var show_SERVICE_UiBtn:ShowServiceButton = new ShowServiceButton();
			show_SERVICE_UiBtn.x = 20;
			show_SERVICE_UiBtn.y = 100 + 50 * layoutMultiplier;
			show_SERVICE_UiBtn.label = '_SERVICE_';
			show_SERVICE_UiBtn.addEventListener(MouseEvent.CLICK, show_SERVICE_UiBtnClickHandler);
			addChild(show_SERVICE_UiBtn);
			layoutMultiplier++;
			/*ACG_SERVICE*/
		}
		
		private function showServiceUi(newUi:Sprite):void{
			if(_serviceUi){
				removeChild(_serviceUi);
			}
			_serviceUi = newUi;
			_serviceUi.x = 150;
			_serviceUi.y = 50;
			addChild(_serviceUi);
		}
		
		/*ACG_SERVICE*/		
		private function show_SERVICE_UiBtnClickHandler(event:MouseEvent):void{
			showServiceUi(new _SERVICE_ClientUi());	
		}
		/*ACG_SERVICE*/
		
	}
}