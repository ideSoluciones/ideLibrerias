package ideLibrerias{
	/**
	*	Clase CImagen
	*/
	import flash.display.Bitmap;
	import flash.display.MovieClip;
	import flash.display.BitmapData;
	import flash.display.Loader;
	import flash.display.DisplayObject;
	import flash.events.*;
	import flash.net.URLRequest;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	import flash.events.*;
	import flash.net.*;
	
	public class CImagen {
		public var padre:MovieClip= new MovieClip();
		private var cargador:Loader = new Loader();
		private var texto:TextField = new TextField();
		private var ancho:Number=100;
		private var alto:Number=100;
		private var centralDeCargaYEventos:URLLoader=new URLLoader();
		private var parametrosGenerales:Object;
		
		public function CImagen(_parametrosGenerales:Object){
			this.parametrosGenerales = _parametrosGenerales;
			this.centralDeCargaYEventos.addEventListener("retorno",this.respuesta);
		}
		
		public function respuesta(event:Event):void{
			var loader:URLLoader = URLLoader(event.target);
            trace("completeHandler: " + loader.data);
			trace("LLEGO RESPUESTA");
		}
		
		private function completeHandler(event:Event):void {
			var tmp:Loader = Loader(event.target.loader);
			this.padre.removeChild(this.texto);

			this.padre.addChild(tmp.content);
			this.padre.width=ancho;
			this.padre.height=alto;
/*			this.padre.graphics.lineStyle(10, 0xFFD700, 1);

            this.padre.graphics.moveTo(0, 0);
            this.padre.graphics.lineTo(100, 0);
            this.padre.graphics.lineTo(100, 100);
            this.padre.graphics.lineTo(0, 100);
            this.padre.graphics.lineTo(0, 0); 
			var a:Object;
			var i:Number=this.padre.numChildren*/
			/*while(i--){
				trace(this.padre.getChildAt(i).name);
				trace("esto es un movieclip");
				
				MovieClip(this.padre.getChildAt(i)).setEventos(this.centralDeCargaYEventos);
				MovieClip(this.padre.getChildAt(i)).hola();
				
				
			}*/
			
			//trace(tmp.content.name);

			//this.padre.graphics.drawRect(0, 0, 100, 100);
			//this.padre.graphics.endFill();
			//this.padre.width=ancho;
			//this.padre.height=alto;
			//trace("Ancho imagen:"+this.padre.width+"\nAlto Imagen:"+this.padre.height);
			/*var imagen:DisplayObject = tmp.content;
			if(ancho>0){
				imagen.width=ancho;
				this.padre.width=ancho;
			}
			if(alto>0){
				imagen.height=alto;
				this.padre.height=alto;
			}
			//trace("Ancho imagen:"+imagen.width+"\nAlto Imagen:"+imagen.height);
			this.padre.addChild(imagen);*/
			
		}
		public function redimensionar(ancho:Number,alto:Number):void{
			this.padre.width=ancho;
			this.padre.height=alto;
		}
		private function ioErrorHandler(event:IOErrorEvent):void {
			this.texto.text="No se pudo cargar la imagen.";
		}
		
		private function ManipuladorDeEventoProgreso(event:ProgressEvent):void {
			var valor:int=event.bytesLoaded*100/event.bytesTotal;
			this.texto.text=valor+"%";
		}
			
		public function cargarImagen(url:String, a:Number, b:Number):void{
		
			this.ancho=a;
			this.alto=b;
		
			var peticion:URLRequest = new URLRequest(this.parametrosGenerales.direccionBase+url);
			var formato:TextFormat = new TextFormat();
			
			this.cargador.contentLoaderInfo.addEventListener(Event.COMPLETE, this.completeHandler);
			this.cargador.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, this.ioErrorHandler);
			this.cargador.contentLoaderInfo.addEventListener(ProgressEvent.PROGRESS, this.ManipuladorDeEventoProgreso);
			this.cargador.load(peticion);

			this.texto.text = "0%";
			this.texto.autoSize = TextFieldAutoSize.LEFT;
			this.texto.background = false;
			this.texto.border = false;
						
			formato.font = "Arial";
			formato.color = 0x3b3b3b;
			formato.size = 10;
			formato.underline = false;
			
			this.texto.setTextFormat(formato);
			this.padre.addChild(texto);
		}
		
		public function getImagen():DisplayObject{
			return this.padre;
		}
	}
}
