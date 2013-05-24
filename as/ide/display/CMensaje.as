package ide.display {
	
	import flash.filters.*;
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	import ide.display.*;
	
	
	public class CMensaje extends CideSprite{
		
		public function CMensaje(_xml:XML){
			var mensaje:TextField=this.crearTexto(_xml.@mensaje);
			mensaje.x=0;
			mensaje.y=0;
			this.addChild(mensaje);
			
			
		}
		
	}
}
