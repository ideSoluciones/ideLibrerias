package  {
	import ide.display.*;
	import ide.XML.*;
	import flash.display.*;
    import flash.text.*;
	import flash.events.*;
	import fl.controls.*;
	
	//Para trabajar con javascript
    import flash.external.ExternalInterface;
    

    //import rl.dev.*;

	public class ideMotorXML extends CideSprite {

	    private var listaNodos:Object;
	    private var contadorBotones:Number;
	    private var especificacion: XML;
        private var datos: XML;


        public var contenedorBotonesHijos:CideSprite;
        public var contenedorBotonesPropiedades:CideSprite;
        public var objetoPropiedades:Array;

        private var elementoXML:ideXML;

        public var parametros:Object;

        public var anteriorElementoXMLSeleccionado:ideXML=null;



        public var contadorCosas:Number=0;

		function ideMotorXML():void{
			stage.align     = StageAlign.TOP_LEFT;
			stage.scaleMode = StageScaleMode.NO_SCALE;

			try {
				parametros = LoaderInfo(this.root.loaderInfo).parameters;
			}catch (error:Error){
				trace("error al cargar los parametros");
				return;
			}


			especificacion = new XML(parametros["especificacion"]);
			datos = new XML(parametros["datos"]);

			//trace(datos);


            //trace("Haciendo contenedores botones");
            this.contenedorBotonesHijos= new CideSprite();
            this.contenedorBotonesPropiedades= new CideSprite();

            this.contenedorBotonesHijos.crearRectangulo(0,0,parametros["ancho"]*0.2, parametros["alto"]*0.8,0xCCCCCC, 0x000000);
            this.contenedorBotonesPropiedades.crearRectangulo(0,0,parametros["ancho"], parametros["alto"]*0.2,0xCCCCCC, 0x000000);
            this.contenedorBotonesPropiedades.y=parametros["alto"]*0.8;
            //trace("Analizando XML");



            //this.addChild(this.contenedorBotonesHijos);
            //this.addChild(this.contenedorBotonesPropiedades);



            //trace("Contenedores botones hechos");

			var nodo:XML;
			var nombreNodo:String;
            listaNodos = new Object();
            contadorBotones=0;

			for each (nodo in especificacion.children()) {
                nombreNodo=""+nodo.@nombre;
                listaNodos[nombreNodo]=nodo;
			}

			trace("1");
		    elementoXML= new ideXML(listaNodos, especificacion.@inicial, this);
		    elementoXML.x=parametros["ancho"]*0.2;
		    elementoXML.y=0;
			trace("2");
			addChild(elementoXML);
			trace("3");
            //seleccionarElemento(elementoXML);
			trace("4");

            ExternalInterface.addCallback("actualizarContenidos", actualizarContenidos);

		    /*
		    var boton:Button;
            boton= crearBoton("Actualizar", actualizarContenidosInterno);
            boton.x=200;
            boton.y=30;
            //boton.visible=false;
            addChild(boton);
			*/

		}
		public function actualizarPropiedadesAnteriorElementoSeleccionado():void{
		    //trace("*Actualizando elemento anterior 1");
            if (anteriorElementoXMLSeleccionado==null)
                return;
		    //trace("*Actualizando elemento anterior 2");
            var i:Number;
            //trace("*Actualizando elemento Viejo "+anteriorElementoXMLSeleccionado.contadorPropiedades+" Elementos");
            for (i=0;i<anteriorElementoXMLSeleccionado.contadorPropiedades;i++){
    		    //trace("*Actualizando elemento anterior 3");
                anteriorElementoXMLSeleccionado.objetoPropiedades[i]=this.objetoPropiedades[i].text;
                //trace("Nombre:"+anteriorElementoXMLSeleccionado.objetoNombrePropiedades[i]+"  Valor:"+this.objetoPropiedades[i].text);
    		    //trace("*Actualizando elemento anterior 4");
            }
		    //trace("*Actualizando elemento anterior 5 - FIN");

		}
		public function actualizarPropiedadesNuevoElementoSeleccionado(nodo:ideXML):void{
		    //trace("+Actualizando elemento nuevo 1");
            var i:Number;
           // trace("+Actualizando elemento nuevo "+nodo.contadorPropiedades+" Elementos");
            for (i=0;i<nodo.contadorPropiedades;i++){
    		    //trace("+Actualizando elemento nuevo 2");
    		   // trace("+Toca agregar "+nodo.objetoPropiedades[i]+" esto en ["+i+"]"+this.objetoPropiedades[i]);
                this.objetoPropiedades[i].text=nodo.objetoPropiedades[i];
    		    //trace("+Actualizando elemento nuevo 3");
            }
		   // trace("+Actualizando elemento nuevo 4 - FIN");

		}
		public function actualizarTx(e:Event):void{
    		actualizarPropiedadesAnteriorElementoSeleccionado();
		}
		public function seleccionarElemento(nodo:ideXML):void{
/*
            if (anteriorElementoXMLSeleccionado==null){
                trace("***** ESTO SOLO OCURRE LA PRIMERA VES");
            }else{
                trace("Actualizando: "+nodo.nombreXML);
                trace("-[old "+anteriorElementoXMLSeleccionado.nombreXML+", new "+nodo.nombreXML+"]");
                trace(",[old "+anteriorElementoXMLSeleccionado.ida+", new "+nodo.ida+"]");
            }
*/

            actualizarPropiedadesAnteriorElementoSeleccionado();
            //trace("Fin actualización viejo");


            anteriorElementoXMLSeleccionado=nodo;


            //trace("Vamos a limpiar propiedades");
            contenedorBotonesPropiedades.limpiar();
            contenedorBotonesHijos.limpiar();
            //trace("propiedades limpia");

            var xml:XML=listaNodos[nodo.nombreXML];
            var parametros:XML;

            var contadorBotones:Number=0;
		    var tx:TextField;
            var boton:Button;

            nodo.contadorPropiedades=0;
            this.objetoPropiedades= new Array;

            for each(parametros in xml.children()){
			    //trace("Analizando "+parametros.name()+" - "+parametros.@nombre);
			    if(parametros.name()=="Propiedad"){
                    tx=crearTexto(parametros.@nombre);
                    tx.x=10;
                    tx.y=20+nodo.contadorPropiedades*20;
                    contenedorBotonesPropiedades.addChild(tx);

                    //trace("P:"+nodo.objetoPropiedades[nodo.contadorPropiedades]);
                    if (nodo.objetoPropiedades[nodo.contadorPropiedades]!=undefined){
                        tx=crearTexto(nodo.objetoPropiedades[nodo.contadorPropiedades]);
                    }else{
                        tx=crearTexto(parametros.@valor);
                    }
                    //tx=crearTexto(nodo.objetoPropiedades[nodo.contadorPropiedades]);

                    tx.x=80;
                    tx.y=20+nodo.contadorPropiedades*20;
                    tx.type = TextFieldType.INPUT;
                    tx.background = true;
                    tx.selectable=true;
                    tx.border=true;
                    tx.height=20;

                    tx.addEventListener(Event.CHANGE, actualizarTx);


                    //trace("Agregando "+parametros.@nombre+" en "+contadorPropiedades);
                    nodo.objetoPropiedades[nodo.contadorPropiedades]=parametros.@valor;
                    nodo.objetoNombrePropiedades[nodo.contadorPropiedades]=parametros.@nombre;
                    this.objetoPropiedades[nodo.contadorPropiedades]=tx;

                    contenedorBotonesPropiedades.addChild(tx);
                    nodo.contadorPropiedades++;
			    }

			    if(parametros.name()=="Hijo"){
		            boton= crearBoton(parametros.@nombre, agregarHijo);
                    boton.x=0;
                    boton.y=contadorBotones*20;
                    //boton.visible=false;
                    contenedorBotonesHijos.addChild(boton);
                    contadorBotones++;
			    }
			 }

            //actualizarPropiedadesNuevoElementoSeleccionado(nodo);
            //trace("Fin actualización nuevo");

		}
		public function agregarHijo(e:MouseEvent):void{
		    //trace("Click");
		    var button:Button = e.target as Button;
		    var elementoXMLNuevo:ideXML= new ideXML(listaNodos, button.label, this, anteriorElementoXMLSeleccionado);
		    elementoXMLNuevo.x=30;
		    elementoXMLNuevo.y=30;
			//this.botonesHijos.visible=false;
            anteriorElementoXMLSeleccionado.objetoHijos.push(elementoXMLNuevo);
			anteriorElementoXMLSeleccionado.Hijos.addChild(elementoXMLNuevo);
		}
        public function actualizarContenidosInterno():void{
            //trace(elementoXML.aCadena());
		    ExternalInterface.call("actualizarVariable", elementoXML.aXML());
        }
        public function actualizarContenidos(mensaje:String):void{
            actualizarContenidosInterno();
		}
	}
}

