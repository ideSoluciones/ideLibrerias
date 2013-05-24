package ideLibrerias{

	/**
	Clase CItemRespuesta
	version 1,0
	29/01/2009
	 */

	import fl.containers.*;
    import fl.events.ScrollEvent;
	import flash.display.MovieClip;
	import flash.text.*;
	
	public class CItemRespuesta{
		public var itemContenedor:MovieClip=new MovieClip();
		public var contenedor:ScrollPane;
		public var itemRespuesta:MovieClip=new MovieClip();
		public var formatoTexto:TextFormat=new TextFormat();
		public var texto:TextField = new TextField();
		public function CItemRespuesta(textoNuevo:String){
			this.contenedor=new ScrollPane();
			this.formatoTexto.font = "arial";
			this.formatoTexto.color = 0x00FF00;
			this.formatoTexto.size = 12;
			this.formatoTexto.underline = false;
			this.formatoTexto.align=TextFormatAlign.JUSTIFY;
			
			this.texto.text = textoNuevo;
			this.texto.alwaysShowSelection = true;
			this.texto.wordWrap=true;
			this.texto.autoSize = TextFieldAutoSize.LEFT;
			this.texto.background = false;
			this.texto.border = false;
			this.texto.setTextFormat(this.formatoTexto);
			this.texto.x=0;
			this.texto.y=0;
			this.texto.width=200;
			
			this.itemRespuesta.addChild(this.texto);
			this.contenedor.setSize(100,100);
			this.contenedor.verticalScrollPolicy="on";
			//this.contenedor.content=this.itemRespuesta;
			

			
			this.itemContenedor.addChild(this.contenedor);
		}
		public function setFormatoTexto(formato:TextFormat){
			this.formatoTexto=formato;
			this.texto.setTextFormat(this.formatoTexto);
		}
		public function getItemRespuesta(){
			return this.itemContenedor;
		}
	}
}