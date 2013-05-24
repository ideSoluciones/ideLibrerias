package ideLibrerias{
	/**
	Clase CContenedor
	version 1,0
	29/01/2009
	 */
	import flash.display.*;
	import flash.text.*;
	import flash.events.*;
	
	public class CContenedor extends Sprite {
		
		private var contenedor:MovieClip=new MovieClip();
		private var contenido:MovieClip=new MovieClip();
		private var centralDeCargaYEventos:IEventDispatcher;
		private var flechaSuperiorScrollVertical:CCargador;
		
		public function CContenedor():void{
			this.contenedor=new MovieClip();
			this.contenido=new MovieClip();
			this.centralDeCargaYEventos=new CEscuchador();
			this.flechaSuperiorScrollVertical= new CCargador();
			var miContenido:MovieClip;
			centralDeCargaYEventos.addEventListener("cargaCompleta",this.cargoFlechaSuperiorScrollVertical);
			centralDeCargaYEventos.addEventListener("presiono",this.presionoFlechaSuperiorScrollVertical);
			flechaSuperiorScrollVertical.setControlDeCargaCompleta(this.centralDeCargaYEventos);
			flechaSuperiorScrollVertical.cargarImagen("./objetos/botonFlechaScroll.swf");
			flechaSuperiorScrollVertical.width=21;
			flechaSuperiorScrollVertical.height=21;
			this.addChild(flechaSuperiorScrollVertical);
		}
		
		function cargoFlechaSuperiorScrollVertical(evento:Event):void{
			flechaSuperiorScrollVertical.getMovieClipContenido().setEscuchadorDeEventos(this.centralDeCargaYEventos);
			flechaSuperiorScrollVertical.getMovieClipContenido().miNombre="flechaSuperiorScrollVertical";
		}
		
		function presionoFlechaSuperiorScrollVertical(evento:Event):void{
			trace("Presiono "+flechaSuperiorScrollVertical.getMovieClipContenido().miNombre);
		}
	}
}