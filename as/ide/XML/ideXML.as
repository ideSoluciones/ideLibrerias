package  ide.XML{
	import ide.display.*;
    import flash.text.*;
	import flash.events.*;
	import flash.display.*;
	import flash.geom.*;

    //Para trabajar con scrolls
	//import com.exanimo.containers.*;
import fl.core.UIComponent;

	public class ideXML extends CideSprite {
	    public var botonesHijos:CideSprite=null;
	    public var Hijos:CideSprite=null;
        public var objetoHijos:Array;
	    public var objetoPropiedades:Array;
	    public var objetoNombrePropiedades:Array;
        public var contadorPropiedades:Number;
        
	    public var listaXML:Object;
	    public var nombreXML:String;

		//Esto es para crear el fondo
		public var fondo:CideSprite;
		
		
private var _spr:Sprite;
private var _rect:Rectangle;
private var _resizeTab:Sprite;

		public function ideXML(lista:Object, nombre:String, motor:ideMotorXML, padre:ideXML=null){
			trace("HOla");
//   			var textField:TextField = new TextField;
//			textField.autoSize = TextFieldAutoSize.LEFT;
//			textField.text = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis varius, quam nec interdum molestie, tellus dolor lacinia purus, sagittis auctor sapien nisl accumsan velit. Pellentesque nibh justo, pulvinar vel, viverra at,\nmalesuada sed, ante. Nunc ultrices quam in ipsum. Suspendisse varius elementum quam. Cras posuere ullamcorper metus. Phasellus at neque non eros varius dictum. In augue. Maecenas in nisl sit amet eros porttitor\nmalesuada. Integer mollis. Etiam pulvinar felis quis arcu. Donec consectetuer consectetuer arcu. Suspendisse potenti. Fusce non felis. Donec commodo lorem in magna. Cras ante lectus, fringilla et, auctor id, adipiscing at,\ntellus. Phasellus et sem non nisl malesuada elementum.\n\nVestibulum facilisis. Sed sit amet quam. Aliquam ac orci. Integer eget felis. Praesent nec orci ac velit facilisis facilisis. Vivamus vehicula, pede et imperdiet tristique, mauris nisl tincidunt tortor, a dictum enim mi quis mi.\nPraesent sem orci, sollicitudin nec, facilisis ac, aliquam id, lectus. Nulla leo. Donec iaculis euismod velit. Suspendisse potenti.\n\nDonec vitae massa. Aenean dapibus. Mauris est. Sed vel nisl sed lectus ullamcorper volutpat. Aliquam quis dui vitae elit vehicula vestibulum. Suspendisse pulvinar. Praesent rutrum, est non gravida elementum, nulla odio\nmolestie magna, sed adipiscing nunc quam vulputate tortor. Ut eu lectus nec augue porta scelerisque. Fusce tincidunt ligula non ipsum. In mauris quam, sagittis et, porttitor sed, commodo id, enim. Vivamus volutpat urna ac\nmi. Cras ut est. Sed vel diam. Mauris mi.\n\nPellentesque auctor felis vitae turpis. Donec quis odio vel orci feugiat commodo. Ut egestas. Etiam imperdiet adipiscing lacus. Praesent dolor. Donec auctor ante vel orci. Quisque tempor aliquet ligula. Aliquam imperdiet nunc\nnec leo. Phasellus mollis rutrum risus. Curabitur felis urna, posuere vel, semper in, molestie vitae, justo. Maecenas ultricies lacinia urna. In congue pede. Mauris et urna nec arcu convallis luctus. Nunc justo pede, faucibus non,\nvarius at, semper et, diam.\n\nNullam lobortis luctus purus. Suspendisse vitae felis non tortor aliquet luctus. Aliquam erat volutpat. Sed a diam. Donec rutrum, turpis quis rutrum convallis, ligula nisl consequat purus, id faucibus libero nunc eget ligula.\nAenean tellus enim, suscipit et, elementum non, venenatis vel, lacus. Integer venenatis commodo est. Phasellus luctus condimentum elit. Aliquam facilisis tempor lacus. Aenean interdum mattis risus. Donec ut pede id mi\naliquet placerat. Vivamus metus nulla, consectetuer id, viverra eu, feugiat sit amet, sem. Nulla ut odio. Fusce nunc. Phasellus facilisis congue nisl. Aenean posuere luctus tortor. Praesent vitae ante. Fusce at leo et est feugiat\nmalesuada. Phasellus tincidunt, nisi a luctus ultricies, nisl lectus semper sem, a vestibulum nulla risus id purus.\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis varius, quam nec interdum molestie, tellus dolor lacinia purus, sagittis auctor sapien nisl accumsan velit. Pellentesque nibh justo, pulvinar vel, viverra at,\nmalesuada sed, ante. Nunc ultrices quam in ipsum. Suspendisse varius elementum quam. Cras posuere ullamcorper metus. Phasellus at neque non eros varius dictum. In augue. Maecenas in nisl sit amet eros porttitor malesuada.\nInteger mollis. Etiam pulvinar felis quis arcu. Donec consectetuer consectetuer arcu. Suspendisse potenti. Fusce non felis. Donec commodo lorem in magna. Cras ante lectus, fringilla et, auctor id, adipiscing at,\ntellus. Phasellus et sem non nisl malesuada elementum.\n\nVestibulum facilisis. Sed sit amet quam. Aliquam ac orci. Integer eget felis. Praesent nec orci ac velit facilisis facilisis. Vivamus vehicula, pede et imperdiet tristique, mauris nisl tincidunt tortor, a dictum enim mi quis mi.\nPraesent sem orci, sollicitudin nec, facilisis ac, aliquam id, lectus. Nulla leo. Donec iaculis euismod velit. Suspendisse potenti.\n\nDonec vitae massa. Aenean dapibus. Mauris est. Sed vel nisl sed lectus ullamcorper volutpat. Aliquam quis dui vitae elit vehicula vestibulum. Suspendisse pulvinar. Praesent rutrum, est non gravida elementum, nulla odio\nmolestie magna, sed adipiscing nunc quam vulputate tortor. Ut eu lectus nec augue porta scelerisque. Fusce tincidunt ligula non ipsum. In mauris quam, sagittis et, porttitor sed, commodo id, enim. Vivamus volutpat urna ac mi.\nCras ut est. Sed vel diam. Mauris mi.\n\nPellentesque auctor felis vitae turpis. Donec quis odio vel orci feugiat commodo. Ut egestas. Etiam imperdiet adipiscing lacus. Praesent dolor. Donec auctor ante vel orci. Quisque tempor aliquet ligula. Aliquam imperdiet nunc\nnec leo. Phasellus mollis rutrum risus. Curabitur felis urna, posuere vel, semper in, molestie vitae, justo. Maecenas ultricies lacinia urna. In congue pede. Mauris et urna nec arcu convallis luctus. Nunc justo pede, faucibus non,\nvarius at, semper et, diam.\n\nNullam lobortis luctus purus. Suspendisse vitae felis non tortor aliquet luctus. Aliquam erat volutpat. Sed a diam. Donec rutrum, turpis quis rutrum convallis, ligula nisl consequat purus, id faucibus libero nunc eget ligula.\nAenean tellus enim, suscipit et, elementum non, venenatis vel, lacus. Integer venenatis commodo est. Phasellus luctus condimentum elit. Aliquam facilisis tempor lacus. Aenean interdum mattis risus. Donec ut pede id mi\naliquet placerat. Vivamus metus nulla, consectetuer id, viverra eu, feugiat sit amet, sem. Nulla ut odio. Fusce nunc. Phasellus facilisis congue nisl. Aenean posuere luctus tortor. Praesent vitae ante. Fusce at leo et est feugiat\nmalesuada. Phasellus tincidunt, nisi a luctus ultricies, nisl lectus semper sem, a vestibulum nulla risus id purus.'
			
			//fondo.move(10,10);
            //fondo.setSize(200,100);
			var ui:UIComponent = new UIComponent();
			trace("HOla2");
			_spr = new Sprite();
			_spr.graphics.clear();
			_spr.graphics.beginFill(0xFF0000,0.5);
			_spr.graphics.drawRect(0,0,200,200);
			_spr.graphics.endFill();
			ui.addChild(_spr);
			addChild(ui);
			trace("HOla3");
			ui.x = 50;
			ui.y = 20;

			_spr.addEventListener(MouseEvent.MOUSE_DOWN,onStartResize);
		    trace("CHao");
		}
		
		
		
		public function onStartResize(e:MouseEvent):void{
			var _rect:Rectangle = _spr.getBounds(_spr.parent);
			if(!_resizeTab)
			{
				_resizeTab = new Sprite();
				_resizeTab.graphics.clear();
				_resizeTab.graphics.beginFill(0x0000FF,0.8);
				_resizeTab.graphics.drawRect(0,0,20,20);
				_resizeTab.graphics.endFill();

				_resizeTab.x = _rect.width - 10;
				_resizeTab.y = _rect.height - 10;

				_spr.addChild(_resizeTab);
				_resizeTab.cacheAsBitmap = false;
				_resizeTab.addEventListener(MouseEvent.MOUSE_DOWN,onResizeDown);
				_resizeTab.addEventListener(MouseEvent.MOUSE_UP,onResizeUp);
			}
			else
				return;
		}

		private function onResizeDown(evt:MouseEvent):void
		{
			var _sp:Sprite = evt.target.parent as Sprite;
			_rect = _sp.getBounds(_sp.parent);
			_resizeTab.startDrag();
			addEventListener(MouseEvent.MOUSE_MOVE,onMove);
		}

		private function onMove(evt:MouseEvent):void
		{
			var _sp:Sprite = _resizeTab.parent as Sprite;
			_rect.bottomRight = new Point(_sp.mouseX,_sp.mouseY);
			_sp.graphics.clear();
			_sp.graphics.beginFill(0xFF0000,0.4);
			_sp.graphics.drawRect(0,0,_rect.width,_rect.height);
			_sp.graphics.endFill();

			var summa:Rectangle = _resizeTab.getBounds(_resizeTab.parent);
			_resizeTab.x = _rect.width - (summa.width/2);
			_resizeTab.y = _rect.height - (summa.height/2);
			evt.updateAfterEvent();
		}

		private function onResizeUp(evt:MouseEvent):void
		{
			removeEventListener(MouseEvent.MOUSE_MOVE,onMove);
			_resizeTab.stopDrag();
		}











		public function aXML(base:XML=null):String{
    		var x:ideXML;
    		var xml:XML;
    		var i:Number;

    		var respuesta:String;
    		xml=this.listaXML[this.nombreXML];

            var nodoRespuesta:XML= new XML("<"+xml.@etiqueta+"/>");
            for(i=0;i<contadorPropiedades;i++){
		        nodoRespuesta[objetoNombrePropiedades[i]]=objetoPropiedades[i];
            }
		    for each(x in this.objetoHijos){
                nodoRespuesta.appendChild(x.aXML());
		    }
		    return nodoRespuesta;
		}
	}		
}
