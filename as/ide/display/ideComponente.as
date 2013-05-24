package ide.display  
{
	import mx.core.*;
	import mx.core.Application;

	import mx.controls.Alert;
	import mx.controls.*;
	import flash.display.*;
	import mx.managers.DragManager;
	import mx.core.DragSource;
	import mx.events.DragEvent;
	import flash.events.MouseEvent;
	import flash.events.Event;
	import mx.containers.Canvas;
	import mx.containers.Panel;
	import com.dyd.DragPanel;
	import flash.external.*;

	import com.dynamicflash.util.*;
	import flash.net.*;
	import flash.utils.Dictionary;
      
	/**
	* ideComponente class.
	* @author jag2kn
	*/
	public class ideComponente extends DragPanel
	{
		public var base:ideBase;
		public var padre:UIComponent;
		public var especificacion:XML;
		public var datos:XML;
		public var nodoEspecificacion:XML;
		public var numero:Number;
		private var nombresCampos:Array;
		private var valoresCampos:Array;

		
		public function setBase(_base:ideBase):void{
			this.base=_base;
		}
		public function setEspecificacion(_especificacion:XML):void{
			this.especificacion=_especificacion;
		}
		public function setDatos(_datos:XML):void{
			this.datos=_datos;
		}
		
		/**
		* Constructor.
		*/
		public function ideComponente() {
			super();
		}
		public function procesaInformacion(inicial:String):void{
			var nodos:XML;
			var hijos:XML;
			
			var button : Button;
			var label : Label;
			var numeroPropiedades:Number=0;
			nombresCampos = new Array();
			valoresCampos = new Array();
			for each(nodos in especificacion.children()){
				if (nodos.@nombre==inicial){
					nodoEspecificacion=nodos;
					for each(hijos in nodos.children()){
						if(hijos.name()=="Campo"){
							label = new Label(); 
							label.text = hijos.@nombre+": "+hijos.@valorPorDefecto; 
							this.addChild(label);
							nombresCampos[numeroPropiedades]=hijos.@nombre;
							//Alert.show("Se agrega "+hijos.@nombre+" en index "+numeroPropiedades);
							valoresCampos[numeroPropiedades]=label;
							numeroPropiedades++;
						}
					}
					if (numeroPropiedades>0){
						button = new Button(); 
						button.label = "Editar"; 
						button.addEventListener(MouseEvent.CLICK, editarCampos);
						this.addChild(button);
					}
					for each(hijos in nodos.children()){
						if(hijos.name()=="Hijo"){
							button = new Button(); 
							button.label = hijos.@nombre; 
							button.addEventListener(MouseEvent.CLICK, agregarHijo);
							this.addChild(button);
						}
					}
				}
			}
		}
		public function procesar(variables:Array, valores:Array):void{
			var nombre:String;
			var contador:Number;
			var index:Number;
			var label:Label;
			for (contador=0;contador<variables.length;contador++){
				for (index=0;index<nombresCampos.length;index++){					
					//Alert.show("Buscando "+variables[contador]+" index="+index+" -> "+variables[contador]+" vs "+nombresCampos[index]);
					if (variables[contador]==nombresCampos[index]){
						label=Label(valoresCampos[index]);
						label.text=variables[contador]+": "+valores[contador];
						//Alert.show("R:/"+variables[contador]+"="+valores[contador]);
					}
					//campos[hijos.@nombre]
				}
			}
		}
		public function editarCampos(evento:MouseEvent):void{
			var parametros:Object=base.parametros;
			var idCasoUso:Number;
			var nombreCasoUso:String;
			var destino:String;
			var idSwf:String;
			for (var i:String in parametros) {
				//Alert.show(i+" - "+parametros[i]);
				// Esto lo hago por que no he podido acceder a parametros["idCasoUso"] directamente :S
				if (i=="idCasoUso"){
					idCasoUso=parametros[i];
				}
				if (i=="nombreCasoUso"){
					nombreCasoUso=parametros[i];
				}
				if (i=="idContenedor"){
					destino=parametros[i];
				}
				if (i=="idSwf"){
					idSwf=parametros[i];
				}
			}


		
			var hijos:XML;

			var cadena:String="<Campo tipo='Enviar' titulo='Guardar' "+
						"id='botonDuplicarDatos' "+
						"click='sendToActionScript"+idSwf+"(\"#"+idSwf+"DatosFormulario\"); $(\"#"+destino+"\").html(\"\"); return false;'  />";
			//Alert.show(cadena);				
			var guardar:XML= new XML(cadena);
			
			//Alert.show("Ya se creo el xml base");

			
		
			
			var formulario:XML=new XML("<Formulario >"+
						"<Propiedad nombre='idCasoUso' valor='"+idCasoUso+"' />"+
						"<Propiedad nombre='id' valor='"+idSwf+"DatosFormulario' />"+
						"<Campo nombre='idComponente' tipo='oculto' valorPorDefecto='"+numero+"' mostrarOculto='true' />"+
							"</Formulario>");
			for each(hijos in nodoEspecificacion.children()){
				if(hijos.name()=="Campo"){
					formulario.appendChild(hijos);
				}
			}
			formulario.appendChild(guardar);
			

			//ExternalInterface.call("cargarDatosRemplazando("+nombreCasoUso+", '#"+destino+"', 'hola mundo')");
			//var myPattern:RegExp = /&&quot;/;
			cadena=formulario.toString();
			//cadena=cadena.replace(myPattern, "\'");
			cadena=cadena.split("\&quot;").join("'");
			//Alert.show(cadena);
			//Alert.show("La cadena es:");
			//cadena=Base64.encode(cadena);
			//Alert.show(cadena);
			//Alert.show("La cadena con remplazo es:");
			ExternalInterface.call("cargarDatosRemplazando","pedirDatos", "#"+destino, "especificacion="+cadena+"&encode64=false");
			//ExternalInterface.call("alert", "hola mundo");

			/*
			Alert.show("Caso de uso: "+parameteros["idCasoUso"]);
			//Alert.show(nodoEspecificacion.toString());
			//var variables:URLVariables = new URLVariables();
			Alert.show("Agregando "+formulario.toString());
			//cadenaVariables
			//variables.decode(cadenaVariables);
			*/
		}
		public function agregarHijo(e : MouseEvent) : void { 
			this.base.crearComponente(this,e.target.label, e.target.label, this.x+this.width+10, this.y+40);
		}
	
		public function actualizar():void {
			this.updateDisplayList(width, height);
		}
		override protected function updateDisplayList(unscaledWidth:Number, unscaledHeight:Number):void {
			super.updateDisplayList(unscaledWidth, unscaledHeight);
			if (this.numero!=0){
				this.graphics.lineStyle(1,0x0099FF);
				this.graphics.moveTo(padre.x+padre.width-x, padre.y+padre.height-y);
				this.graphics.lineTo(0, 0);
			}
			
			
		}
		override public function toString():String{
			return "ideComponente";
		}

	}
}
