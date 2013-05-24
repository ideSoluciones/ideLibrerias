package ideLibrerias{
	/**
	Clase CPanel
	version 1,0
	29/01/2009
	 */
	import flash.display.*; 
	import flash.events.*; 
	import flash.geom.Rectangle; 
	import fl.transitions.Tween; 
	import fl.transitions.easing.*;
	import flash.text.*;
	
	public class CScroll extends Sprite {
		
		private var centralDeCargaYEventos:IEventDispatcher;
		private var flechaSuperiorScroll:CCargador;
		private var flechaInferiorScroll:CCargador;
		private var cuerpoScroll:CCargador;
		private var objetosCargados:int=0;
		private var objetosACargar:int=2;
		private var texto:TextField;
		
		public function CScroll():void{
			
			this.centralDeCargaYEventos=new CEscuchador();
			this.flechaSuperiorScroll= new CCargador();
			this.flechaInferiorScroll= new CCargador();
			this.cuerpoScroll= new CCargador();
			
			var formato:TextFormat = new TextFormat();
			this.texto=new TextField();
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
			
			this.centralDeCargaYEventos.addEventListener("cargaCompleta",this.cargoObjetoScroll);
			
			this.cuerpoScroll.setControlDeCargaCompleta(this.centralDeCargaYEventos);
			this.cuerpoScroll.cargarImagen("./objetos/cuerpoScroll.swf");
			
			this.flechaSuperiorScroll.setControlDeCargaCompleta(this.centralDeCargaYEventos);
			this.flechaSuperiorScroll.cargarImagen("./objetos/botonFlechaScroll.swf");
			
			this.flechaInferiorScroll.setControlDeCargaCompleta(this.centralDeCargaYEventos);
			this.flechaInferiorScroll.cargarImagen("./objetos/botonFlechaScroll.swf");
		
		}
		
		function cargoObjetoScroll(evento:Event):void{
			switch(objetosCargados){
				case 0:	case 1:	break;
				case 2:
					
					this.addChild(this.cuerpoScroll);
					this.addChild(this.flechaSuperiorScroll);
					this.addChild(this.flechaInferiorScroll);
					
					this.flechaInferiorScroll.y=this.cuerpoScroll.height-this.flechaInferiorScroll.height;
					
					this.cuerpoScroll.addEventListener(MouseEvent.MOUSE_WHEEL,accionWheel);
					this.flechaSuperiorScroll.addEventListener(MouseEvent.CLICK,presionoFlechaSuperiorScroll);
					this.flechaInferiorScroll.addEventListener(MouseEvent.CLICK,presionoFlechaInferiorScroll);
					
					this.removeChild(this.texto);
					break;
			}
			this.objetosCargados++;
			ManipuladorDeEventoProgreso();
		}
		
		private function ManipuladorDeEventoProgreso():void {
			var valor:int=this.objetosCargados*100/objetosACargar;
			this.texto.text=valor+"%";
		}
		
		private function presionoFlechaSuperiorScroll(evento:Event):void{
			trace("Arriba");
		}
		
		private function presionoFlechaInferiorScroll(evento:Event):void{
			trace("Abajo");
		}
		
		private function accionWheel(evento:MouseEvent){
			trace("Wheel");
		}
	}
}