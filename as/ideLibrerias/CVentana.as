package ideLibrerias{
	/**
	Clase CVentana
	version 1,0
	29/01/2009
	 */
	import flash.display.MovieClip;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	
	public class CVentana{
		
		private var ventana:MovieClip=new MovieClip();
		private var titulo:TextField = new TextField();
		private var colorFondo="0xFFFFFF";
		private var anchoVentana=100;
		private var altoVentana=100;
		private var xVentana=0;
		private var yVentana=0;
		private var transparencia=0.9;
		private var colorOImagenDeFondo=1;
		private var imagenDeFondo;
		
		public function redimensionar(ancho:Number,alto:Number){
			this.anchoVentana=ancho;
			this.altoVentana=alto;
		}
		public function posicionar(xNuevo:int,yNuevo:int){
			this.xVentana=xNuevo;
			this.yVentana=yNuevo;
		}
		public function setColorFondo(color){
			this.colorFondo=color;
			this.colorOImagenDeFondo=1;
		}
		public function setImagenDeFondo(url){
			this.imagenDeFondo=url;
			this.colorOImagenDeFondo=2;
		}
		public function setTransparencia(color){
			this.transparencia=color;
		}
		public function pintarVentana(){
			
			switch(colorOImagenDeFondo){
				case 2:
					var tmp= new CImagen();
					tmp.cargarImagen(this.imagenDeFondo);
					this.agregarObjeto(tmp.getImagen(),0,0);
				case 1:
					this.ventana.graphics.beginFill(this.colorFondo);
					this.ventana.graphics.drawRect(this.ventana.x, this.ventana.y, this.anchoVentana, this.altoVentana);
					this.ventana.graphics.endFill();
					break;
			}
			this.ajustarVentana();
		}
		private function ajustarVentana(){
			this.ventana.alpha=this.transparencia;
			this.ventana.width=this.anchoVentana;
			this.ventana.height=this.altoVentana;
			this.ventana.x=this.xVentana;
			this.ventana.y=this.yVentana;
		}
		public function agregarTexto(textoNuevo:String,xx:int,yy:int,formato:TextFormat){
			var texto:TextField = new TextField();
			texto.text = textoNuevo;
			texto.autoSize = TextFieldAutoSize.LEFT;
			texto.background = false;
			texto.border = false;
			texto.setTextFormat(formato);
			texto.x=xx;
			texto.y=yy;
			this.ventana.addChild(texto);
		}
		public function agregarObjeto(objeto:MovieClip,xx,yy){
			objeto.x=xx;
			objeto.y=yy;
			this.ventana.addChild(objeto);
		}
		public function getVentana(){
			return this.ventana;
		}
	}
}