package ide.display{
	import flash.display.*;
	import fl.containers.*;
	import flash.events.*;
	import flash.net.*;
	import flash.utils.Timer;
	import flash.text.*;
	import fl.controls.*;
	import fl.data.*;
	import ide.events.*;

    //import rl.dev.*;



	public class CideSprite extends Sprite{

		public var banderaVisible:Boolean;
		public var propiedades:Object;
		public var contadorTraces:Number;
		public var fuenteTrace:String;
		public var traces:CideSprite;
		
		

		//private var anunciador:DispatcherObject;

		public function CideSprite(){
			fuenteTrace="Arial";
			contadorTraces=0;
			this.propiedades= new Object();
			this.traces=null;


		}

		//Functiones para realizar arrastre, drag
		public function drageable(sp:Sprite, iniciaRedimencion:Function=null, terminaRedimencion:Function=null):void{
		    sp.addEventListener(MouseEvent.MOUSE_DOWN, onMouse_startDrag);
		    if (iniciaRedimencion!=null)
		    	sp.addEventListener(MouseEvent.MOUSE_DOWN, iniciaRedimencion);
            sp.addEventListener(MouseEvent.MOUSE_UP, onMouse_stopDrag);
		    if (terminaRedimencion!=null)
		    	sp.addEventListener(MouseEvent.MOUSE_UP, terminaRedimencion);
		}
		public function onMouse_startDrag(e:Event):void{
			stopDrag();
			e.currentTarget.startDrag();
		}
		public function onMouse_stopDrag(e:Event):void{
			stopDrag();
			e.currentTarget.stopDrag();
		}
		public function drageablePadre(sp:Sprite, iniciaRedimencion:Function=null, terminaRedimencion:Function=null):void{
		    sp.addEventListener(MouseEvent.MOUSE_DOWN, onMouse_startDragPadre);
		    if (iniciaRedimencion!=null)
		    	sp.addEventListener(MouseEvent.MOUSE_DOWN, iniciaRedimencion);
            sp.addEventListener(MouseEvent.MOUSE_UP, onMouse_stopDragPadre);
		    if (terminaRedimencion!=null)
		    	sp.addEventListener(MouseEvent.MOUSE_UP, terminaRedimencion);
		}
		public function onMouse_startDragPadre(e:Event):void{
			stopDrag();
			e.currentTarget.parent.startDrag();
		}
		public function onMouse_stopDragPadre(e:Event):void{
			stopDrag();
			e.currentTarget.parent.stopDrag();
		}



		//Funciones para cargar fuentes en tiempo de ejecución
		public function cargarFuente(url:String, nombre:String):void {
			//trace("Solucito cargar una fuente1 ["+url+", "+nombre+"]");
			fuenteTrace=nombre;
			//trace("Solucito cargar una fuente2");
			var loader:Loader = new Loader();
			//trace("Solucito cargar una fuente3");
			loader.contentLoaderInfo.addEventListener(Event.COMPLETE, fuenteCargada_Interna);
			//trace("Solucito cargar una fuente4");
		//	loader.contentLoaderInfo.addEventListener(Event.COMPLETE, funcionOnLoad);
			loader.load(new URLRequest(url));
			//trace("Solucito cargar una fuente5");
		}

		private function fuenteCargada_Interna(event:Event):void {
			var FontLibrary:Class = event.target.applicationDomain.getDefinition(fuenteTrace) as Class;
			Font.registerFont(FontLibrary._Arial);
			Font.registerFont(FontLibrary._Arial_bold);
			//trace("Termino de cargar la fuente");
			inicio();
		}


        //Funciones para crear elementos graficos facilmente
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

/*
			var botonAgregar:CideSprite= new CideSprite();
			botonAgregar.addChild(crearRectangulo(0,0,80,20, 0xFF9999));
			botonAgregar.addChild(crearTexto(texto));
            this.propiedades["label"]=texto;
            botonAgregar.addEventListener(MouseEvent.CLICK, accion);

            return botonAgregar;
*/
			/* probando estilos, pero no funcionan

            boton.setStyle("fillColors", new Array(0x000000, 0x707070));
            boton.setStyle("color", 0xFFFFFF);
            boton.setStyle("textRollOverColor", 0x0000FF);
            boton.setStyle("themeColor", 0x00007f);

	/*
Button
{
	fillColors: #000000, #707070;
	color: #ffffff;
	textRollOverColor: #0000ff;
	themeColor: #00007f;
}*/


			return boton;
		}

		public function crearTexto(t:String, size:Number=12, alineacion:String="", ancho:Number=0, alto:Number=0, negrilla:String=""):TextField{
			var negrillaT:Boolean;
			if (valido(negrilla) && negrilla=="true"){
				//t="<b>"+t+"</b>";
				negrillaT = true;
				//fuenteTrace="_Arial_bold";
			}else{
				//fuenteTrace="_Arial";
			}

			//Formato para determinar la justificacion, fuente y tamaño
			var format:TextFormat = new TextFormat(fuenteTrace, size, 0, negrillaT);
			switch(alineacion){
			case "centrado":
				format.align=TextFormatAlign.CENTER;
				break;
			case "izquierda":
				format.align=TextFormatAlign.LEFT;
				break;
			case "derecha":
				format.align=TextFormatAlign.RIGHT;
				break;
			case "justificado":
			default:
				format.align=TextFormatAlign.JUSTIFY;
				break;
			}
			
			if (valido(negrilla) && negrilla=="true"){
				format.bold = true;
			}

			//Se crea el texto y se le da el formato
			var tf:TextField = new TextField();
			tf.defaultTextFormat = format;
			if (fuenteTrace!="Arial")
				tf.embedFonts = true;
			tf.antiAliasType = AntiAliasType.ADVANCED;
			tf.selectable=false;
			tf.multiline=true;
			//tf.border = true; // Quitar esto en produccion


			if (ancho==0){
				tf.autoSize = TextFieldAutoSize.LEFT;
			}else{
				tf.width=ancho;
				tf.wordWrap = true;
			}
			
			if (alto!=0)
				tf.height=alto;


			
			tf.setTextFormat(format);
				

			//tf.htmlText = t;
			tf.text = t;
			return tf;
		}
		/*
		public function crearRectangulo(x:Number,y:Number,w:Number,h:Number, colorFondo:Number=0xFFFFFFFF, colorLinea:Number=0x000000FF, bordeLinea:Number=1):Shape{
			var rect:Shape = new Shape();
			rect.graphics.lineStyle(bordeLinea,colorLinea);
			rect.graphics.beginFill(colorFondo);
			rect.graphics.drawRect(x, y, w, h);
			rect.graphics.endFill();
			return rect;
		}*/
		public function crearRectangulo(x:Number,y:Number,w:Number,h:Number, colorFondo:Number=0xFFFFFF, colorLinea:Number=0x000000, bordeLinea:Number=1):void{
			this.graphics.lineStyle(bordeLinea,colorLinea);
			this.graphics.beginFill(colorFondo);
			this.graphics.drawRect(x, y, w, h);
			this.graphics.endFill();
		}


		//Función para validar si algún campo tiene longitud mayor a 0
		public function valido(algo:Object):Boolean{
			var s:String=algo.toString();
			if (s.length>0){
				return true;
			}
			return false;
		}

		public function inicio():void{
			trace("Inicio desde CideSprite");
		}

        //Función para limpiar un cidesprite
        public function limpiar():void{
            //trace("Inicio limpiar ");
            var largo:Number = this.numChildren;
            //trace("El numero de hijos es: "+largo);
            while(largo){
                this.removeChildAt(0);
                largo = this.numChildren;
            }
            largo = this.numChildren;
            //trace("El numero de hijo es: "+largo);

        }

        //Funciones trace
		public function trace(cadena:String, plano:Boolean=false):void {


		    if (traces==null){
                this.traces= new CideSprite();
                this.traces.y=300;
                this.traces.x=0;
                this.traces.crearRectangulo(0, 0, 300, 10, 0xCCCCFFFF, 0x000000FF, 1);
                drageable(this.traces);
                this.addChild(traces);
		    }

			//var texto:TextField=this.crearTexto(cadena);
			var texto:TextField = new TextField();
			texto.defaultTextFormat = new TextFormat("Arial", 12, 0);
			texto.text=cadena;
			//var texto:TextField=this.crearTexto("Trace:");
			texto.border = true;
			texto.selectable=true;
			texto.width=300;
			//texto.alpha=0.5;
			//texto.x=0;
			texto.y=this.contadorTraces*20;

			if(plano){
				texto.text = cadena;
			}


			this.contadorTraces++;
			this.traces.addChild(texto);
			/**
			 * @TODO: Ticket #86: hacer que los textos se desaparezcan al cabo de X tiempo
			 */
		}

	}
}

