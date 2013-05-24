package ide.display{
	import flash.display.*;
	import flash.events.*;
	import flash.net.*;
	import flash.utils.Timer;
	import flash.text.*;
	import fl.controls.*;
	import fl.data.*;



	
	public class CideSprite extends Sprite{
				
		public var banderaVisible:Boolean;
		public var propiedades:Object;
		
		public function crearAreaTexto(texto:String):TextArea{
			var areaTexto:TextArea= new TextArea();
			areaTexto.text=texto;
			return areaTexto;
			
		}
		
		public function crearComboBox(valores:String):ComboBox{
			var combo:ComboBox= new ComboBox();
			var dp:DataProvider = new DataProvider();
			
			combo.dataProvider=dp;
			combo.x=100;
			combo.y=100;
			var cadenas:Array;
			cadenas=valores.split(",");
			//dp.addItem({label:"Vacio", data:"Vacio"});
			for (var k:Number=0;k<cadenas.length;k++){
				//trace("Agregando cadena");
				dp.addItem({label:cadenas[k], data:cadenas[k]});
			}
			return combo;
		}
		
		public function crearBoton(texto:String, accion:Function):Button{
			var boton:Button;
			boton= new Button();
			boton.label=texto;
			boton.width=80;
			boton.addEventListener(MouseEvent.CLICK, accion);			
			return boton;
		}
		
		public function crearTexto(t:String):TextField{
			var label:TextField= new TextField();

			label.text=t;
			label.autoSize = TextFieldAutoSize.LEFT;

			var format:TextFormat = new TextFormat();
			format.font = "Arial";
			format.color = 0x000000;
			format.size = 12;
			label.setTextFormat(format);
			return label;
		}

				
	}
}
