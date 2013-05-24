package  {
	import ideLibrerias.*;
	import flash.display.*;
    import flash.text.*;	
	
	public class Cargador extends Sprite {

		function Cargador():void{
			stage.align     = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;

			try {
				var param:Object = LoaderInfo(this.root.loaderInfo).parameters;
			}catch (error:Error){
				trace("error al cargar los parametros");
			}
			
			/**
			* @ToDo: Ajustar bien las variables para hacer estas determinaciones correctamente
			*/
			if (param["idePelicula"]==undefined){
				param["idePelicula"]="../Librerias/as/asError.swf";
			}else{				
			}
	
			
			var label:TextField = new TextField();
            label.autoSize = TextFieldAutoSize.LEFT;
            label.background = true;
            label.border = true;
            label.text="estoy cargando: ["+param["idePelicula"]+"]";

			label.x=0;
			label.y=30;
			
			
            var format:TextFormat = new TextFormat();
            format.font = "Verdana";
            format.color = 0xFF0000;
            format.size = 10;
            format.underline = true;
            
			label.defaultTextFormat = format;

			addChild(label);

			var imagen:CImagen=new CImagen(new Object());
			imagen.cargarImagen(param["idePelicula"], this.width, this.height);
			addChild(imagen.getImagen());	
		}
	}
}
