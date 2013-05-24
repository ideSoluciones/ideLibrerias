package ideLibrerias{
	/**
	Clase CPanel
	version 1,0
	29/01/2009
	 */
	import flash.display.MovieClip;
	import flash.display.Sprite;
	import flash.text.TextField;
	import flash.text.TextFieldAutoSize;
	import flash.text.TextFormat;
	
	public class CPanel extends Sprite {
		private var mascara:MovieClip=new MovieClip();
		private var contenido:MovieClip=new MovieClip();
		public function CPanel(ancho,alto){
			this.width=ancho;
			this.height=alto;
			this.mascara.graphics.beginFill(0x000000);
			this.mascara.graphics.drawRect(0, 0, 100, 100);
			this.mascara.graphics.endFill();
			this.mascara.width=100;
			this.mascara.height=100;
			var tmp= new CImagen();
			tmp= new CImagen();
			tmp.cargarImagen("http://nosenosocurrio.files.wordpress.com/2008/11/carro-cicleta.jpg");
			this.contenido.addChild(tmp.getImagen());
			this.contenido.mask=this.mascara;
			this.addChild(this.contenido);
			this.addChild(this.mascara);
			trace(this.width+"x"+this.height+","+ancho+"x"+alto);
		}
	}
}