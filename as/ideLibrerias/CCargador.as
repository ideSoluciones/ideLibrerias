package ideLibrerias{
	/**
	*	Clase CImagen
	*	version 1,0
	*	23/10/2008
	*/
	import flash.display.*;
	import flash.events.*;
	import flash.text.*;
	import flash.net.*;
	import flash.geom.*;
	
	public class CCargador extends Sprite {
		
		private var contenido:DisplayObject;
		private var cargador:Loader;
		private var texto:TextField;
		private var contralDeCargaCompleta:IEventDispatcher;
		private var activarContralDeCargaCompleta:Boolean;
		
		public function setContenido(nuevoContenido:DisplayObject){
			this.contenido=nuevoContenido;
		}
		
		public function addContenido(){
			this.addChild(this.contenido);
		}
		
		public function getCopia(){
			var copia:CCargador=new CCargador();
			copia.setContenido(this.contenido);
			copia.addContenido();
			// duplicate properties
			copia.transform = this.transform;
			copia.filters = this.filters;
			copia.cacheAsBitmap = this.cacheAsBitmap;
			copia.opaqueBackground = this.opaqueBackground;
			if (this.scale9Grid) {
				var rect:Rectangle = this.scale9Grid;
				copia.scale9Grid = rect;
			}
			
			return copia;
		}
		
		public function CCargador(){
			this.cargador = new Loader();
			this.texto = new TextField();
			this.activarContralDeCargaCompleta=false;
		}
		
		public function setControlDeCargaCompleta(escuchador:IEventDispatcher):void{
			this.contralDeCargaCompleta=escuchador;
			this.activarContralDeCargaCompleta=true;
		}
		
		public function getMovieClipContenido():MovieClip{
			return MovieClip(this.contenido);
		}
		
		private function cargaCompleta(evento:Event):void {
			var tmp:Loader = Loader(evento.target.loader);
			this.removeChild(this.texto);
			this.contenido=tmp.content;
			this.addContenido();
			if(this.activarContralDeCargaCompleta){
				this.contralDeCargaCompleta.dispatchEvent(new Event("cargaCompleta"));
			}
		}

		private function ocurrioErrorIO(evento:IOErrorEvent):void {
			this.texto.text="No se pudo cargar la imagen ["+evento+"].";
		}
		
		private function ManipuladorDeEventoProgreso(event:ProgressEvent):void {
			var valor:int=event.bytesLoaded*100/event.bytesTotal;
			this.texto.text=valor+"%";
		}
			
		public function cargarImagen(url:String):void{
			var peticion:URLRequest = new URLRequest(url);
			var formato:TextFormat = new TextFormat();
			
			//Configuración de texto indicador de progreso
				this.texto.text = "0%";
				this.texto.autoSize = TextFieldAutoSize.LEFT;
				this.texto.background = false;
				this.texto.border = false;
							
				formato.font = "Arial";
				formato.color = 0x3b3b3b;
				formato.size = 10;
				formato.underline = false;
				
				this.texto.setTextFormat(formato);
				this.addChild(texto);
			//
			
			//Cargando escuchadores
				this.cargador.contentLoaderInfo.addEventListener(Event.COMPLETE, this.cargaCompleta);
				this.cargador.contentLoaderInfo.addEventListener(IOErrorEvent.IO_ERROR, this.ocurrioErrorIO);
				this.cargador.contentLoaderInfo.addEventListener(ProgressEvent.PROGRESS, this.ManipuladorDeEventoProgreso);
			//
			
			//Comenzar carga de imagen
				this.cargador.load(peticion);
			//
		}
	}
}
