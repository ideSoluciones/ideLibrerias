package  ide.XML{
	import ide.display.*;
	import flash.display.*;
    import flash.text.*;
	import flash.events.*;
	import fl.controls.*;
	//import flash.geom.Rectangle;
	//import fl.containers.*;


    //Para trabajar con scrolls
	import com.exanimo.containers.*;


	public class CXML extends CideSprite {

	    public var botonesHijos:CideSprite=null;
	    public var Hijos:CideSprite=null;
	    public var propiedadesHijas:CideSprite;
	    public var listaXML:Object;
	    public var nombreXML:String;
	    public var padreXML:CXML;
	    public var motor:ideMotorXML;
	    private var lineaPadre:Shape;

	    public var contadorBotones:Number;
	    private var botonXmlsHijosVisibles:CideSprite;

	    public var objetoPropiedades:Array;
	    //public var objetoValorPropiedades:Array;
	    public var objetoNombrePropiedades:Array;
        public var contadorPropiedades:Number;
        public var objetoHijos:Array;


        public var ida:Number;

		public var fondoContenedor:EasingScrollPane;
		public var fondo:CideSprite;
		public var resizeador:CideSprite;
		public var colorFondo:Number;
		public var ancho:Number=200;
		public var alto:Number=200;
		public var redimencionando:Boolean;
		
		private var anteriorx:Number;
		private var anteriory:Number;
		
        /*
            Estoy analizando como construir la función que cargue el xml de datos
        */


		function CXML(lista:Object, nombre:String, motor:ideMotorXML, padre:CXML=null):void{
		    trace("Creando CXML");
		    
		    /*
		    this.listaXML=lista;
		    this.nombreXML=nombre;
		    this.padreXML=padre;
		    this.motor=motor;

		    ancho=200;
			alto=200;
			redimencionando=false;
			
		    this.ida=motor.contadorCosas;
		    motor.contadorCosas++;

            this.propiedadesHijas= new CideSprite();
            this.addChild(this.propiedadesHijas);

            if (this.padreXML!=null){
                 this.addEventListener(Event.ENTER_FRAME, repintar);
            }


            this.objetoPropiedades= new Array();
            this.objetoNombrePropiedades= new Array();
            this.objetoHijos= new Array();


		    var xml:XML=listaXML[nombreXML];
            var fondobotonesHijos:Shape;
            var rectangulo:Shape;
		    var tx:TextField;

		    var parametros:XML;

		    contadorPropiedades=0;
		    contadorBotones=0;
		    */


            //this.dibujarElemento();
            
		    trace("Agregando hijo "+nombre);
			this.fondoContenedor = new EasingScrollPane();
   			//this.fondo = new CideSprite();
   			var textField:TextField = new TextField;
			textField.autoSize = TextFieldAutoSize.LEFT;
			textField.text = 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis varius, quam nec interdum molestie, tellus dolor lacinia purus, sagittis auctor sapien nisl accumsan velit. Pellentesque nibh justo, pulvinar vel, viverra at,\nmalesuada sed, ante. Nunc ultrices quam in ipsum. Suspendisse varius elementum quam. Cras posuere ullamcorper metus. Phasellus at neque non eros varius dictum. In augue. Maecenas in nisl sit amet eros porttitor\nmalesuada. Integer mollis. Etiam pulvinar felis quis arcu. Donec consectetuer consectetuer arcu. Suspendisse potenti. Fusce non felis. Donec commodo lorem in magna. Cras ante lectus, fringilla et, auctor id, adipiscing at,\ntellus. Phasellus et sem non nisl malesuada elementum.\n\nVestibulum facilisis. Sed sit amet quam. Aliquam ac orci. Integer eget felis. Praesent nec orci ac velit facilisis facilisis. Vivamus vehicula, pede et imperdiet tristique, mauris nisl tincidunt tortor, a dictum enim mi quis mi.\nPraesent sem orci, sollicitudin nec, facilisis ac, aliquam id, lectus. Nulla leo. Donec iaculis euismod velit. Suspendisse potenti.\n\nDonec vitae massa. Aenean dapibus. Mauris est. Sed vel nisl sed lectus ullamcorper volutpat. Aliquam quis dui vitae elit vehicula vestibulum. Suspendisse pulvinar. Praesent rutrum, est non gravida elementum, nulla odio\nmolestie magna, sed adipiscing nunc quam vulputate tortor. Ut eu lectus nec augue porta scelerisque. Fusce tincidunt ligula non ipsum. In mauris quam, sagittis et, porttitor sed, commodo id, enim. Vivamus volutpat urna ac\nmi. Cras ut est. Sed vel diam. Mauris mi.\n\nPellentesque auctor felis vitae turpis. Donec quis odio vel orci feugiat commodo. Ut egestas. Etiam imperdiet adipiscing lacus. Praesent dolor. Donec auctor ante vel orci. Quisque tempor aliquet ligula. Aliquam imperdiet nunc\nnec leo. Phasellus mollis rutrum risus. Curabitur felis urna, posuere vel, semper in, molestie vitae, justo. Maecenas ultricies lacinia urna. In congue pede. Mauris et urna nec arcu convallis luctus. Nunc justo pede, faucibus non,\nvarius at, semper et, diam.\n\nNullam lobortis luctus purus. Suspendisse vitae felis non tortor aliquet luctus. Aliquam erat volutpat. Sed a diam. Donec rutrum, turpis quis rutrum convallis, ligula nisl consequat purus, id faucibus libero nunc eget ligula.\nAenean tellus enim, suscipit et, elementum non, venenatis vel, lacus. Integer venenatis commodo est. Phasellus luctus condimentum elit. Aliquam facilisis tempor lacus. Aenean interdum mattis risus. Donec ut pede id mi\naliquet placerat. Vivamus metus nulla, consectetuer id, viverra eu, feugiat sit amet, sem. Nulla ut odio. Fusce nunc. Phasellus facilisis congue nisl. Aenean posuere luctus tortor. Praesent vitae ante. Fusce at leo et est feugiat\nmalesuada. Phasellus tincidunt, nisi a luctus ultricies, nisl lectus semper sem, a vestibulum nulla risus id purus.\n\nLorem ipsum dolor sit amet, consectetuer adipiscing elit. Duis varius, quam nec interdum molestie, tellus dolor lacinia purus, sagittis auctor sapien nisl accumsan velit. Pellentesque nibh justo, pulvinar vel, viverra at,\nmalesuada sed, ante. Nunc ultrices quam in ipsum. Suspendisse varius elementum quam. Cras posuere ullamcorper metus. Phasellus at neque non eros varius dictum. In augue. Maecenas in nisl sit amet eros porttitor malesuada.\nInteger mollis. Etiam pulvinar felis quis arcu. Donec consectetuer consectetuer arcu. Suspendisse potenti. Fusce non felis. Donec commodo lorem in magna. Cras ante lectus, fringilla et, auctor id, adipiscing at,\ntellus. Phasellus et sem non nisl malesuada elementum.\n\nVestibulum facilisis. Sed sit amet quam. Aliquam ac orci. Integer eget felis. Praesent nec orci ac velit facilisis facilisis. Vivamus vehicula, pede et imperdiet tristique, mauris nisl tincidunt tortor, a dictum enim mi quis mi.\nPraesent sem orci, sollicitudin nec, facilisis ac, aliquam id, lectus. Nulla leo. Donec iaculis euismod velit. Suspendisse potenti.\n\nDonec vitae massa. Aenean dapibus. Mauris est. Sed vel nisl sed lectus ullamcorper volutpat. Aliquam quis dui vitae elit vehicula vestibulum. Suspendisse pulvinar. Praesent rutrum, est non gravida elementum, nulla odio\nmolestie magna, sed adipiscing nunc quam vulputate tortor. Ut eu lectus nec augue porta scelerisque. Fusce tincidunt ligula non ipsum. In mauris quam, sagittis et, porttitor sed, commodo id, enim. Vivamus volutpat urna ac mi.\nCras ut est. Sed vel diam. Mauris mi.\n\nPellentesque auctor felis vitae turpis. Donec quis odio vel orci feugiat commodo. Ut egestas. Etiam imperdiet adipiscing lacus. Praesent dolor. Donec auctor ante vel orci. Quisque tempor aliquet ligula. Aliquam imperdiet nunc\nnec leo. Phasellus mollis rutrum risus. Curabitur felis urna, posuere vel, semper in, molestie vitae, justo. Maecenas ultricies lacinia urna. In congue pede. Mauris et urna nec arcu convallis luctus. Nunc justo pede, faucibus non,\nvarius at, semper et, diam.\n\nNullam lobortis luctus purus. Suspendisse vitae felis non tortor aliquet luctus. Aliquam erat volutpat. Sed a diam. Donec rutrum, turpis quis rutrum convallis, ligula nisl consequat purus, id faucibus libero nunc eget ligula.\nAenean tellus enim, suscipit et, elementum non, venenatis vel, lacus. Integer venenatis commodo est. Phasellus luctus condimentum elit. Aliquam facilisis tempor lacus. Aenean interdum mattis risus. Donec ut pede id mi\naliquet placerat. Vivamus metus nulla, consectetuer id, viverra eu, feugiat sit amet, sem. Nulla ut odio. Fusce nunc. Phasellus facilisis congue nisl. Aenean posuere luctus tortor. Praesent vitae ante. Fusce at leo et est feugiat\nmalesuada. Phasellus tincidunt, nisi a luctus ultricies, nisl lectus semper sem, a vestibulum nulla risus id purus.'
			
            //fondo.move(10,10);
            //fondo.setSize(ancho,alto);
			//fondo.addChild(textField);
			//fondo.crearRectangulo(0,0,ancho,alto, colorFondo);
            //fondo.addEventListener(MouseEvent.CLICK, seleccionarElemento);
            //fondoContenedor.width=50;
            //fondoContenedor.height=50;
            
            fondoContenedor.source=textField;
            
		    this.addChild(fondoContenedor);


			//trace("Iniciando un xml de: "+xml.@nombre);

		    //trace("Ciclo hijos CXML");
            /*
                Ojo disminuir la acoplación en esta implementación
            */
            /*
            motor.contenedorBotonesHijos;
            motor.contenedorBotonesPropiedades;


            this.Hijos= new CideSprite();
            this.addChild(this.Hijos);
            */
            
/*
			for each(parametros in xml.children()){
			    //trace("Analizando "+parametros.name()+" - "+parametros.@nombre);
			    if(parametros.name()=="Propiedad"){
                    tx=crearTexto(parametros.@nombre);
                    tx.x=10;
                    tx.y=20+contadorPropiedades*20;
                    addChild(tx);

                    tx=crearTexto(parametros.@valor);
                    tx.x=80;
                    tx.y=20+contadorPropiedades*20;
                    tx.type = TextFieldType.INPUT;
                    tx.background = true;
                    tx.selectable=true;
                    tx.border=true;
                    tx.height=20;
                    //trace("Agregando "+parametros.@nombre+" en "+contadorPropiedades);
                    this.objetoPropiedades[contadorPropiedades]=tx;
                    this.objetoNombrePropiedades[contadorPropiedades]=parametros.@nombre;
                    this.addChild(tx);
                    contadorPropiedades++;
			    }

			    if(parametros.name()=="Hijo"){
			        if (this.botonesHijos==null){
			            this.botonesHijos= new CideSprite();
                        this.botonesHijos.x=0;
                        this.botonesHijos.y=30;
            			this.botonesHijos.visible=false;
            			this.botonesHijos.crearRectangulo(0,0,200,200, 0x99FF99);


                        var botonAgregar:CideSprite= new CideSprite();
            			botonAgregar.crearRectangulo(0,0,20,20, 0xFF9999);
            			botonAgregar.crearRectangulo(8,2,4,18, 0x000000, 0x000000);
            			botonAgregar.crearRectangulo(2,8,18,4, 0x000000, 0x000000);
                        botonAgregar.addEventListener(MouseEvent.CLICK, botonesHijosVisibles);
                        this.addChild(botonAgregar);

                        this.botonXmlsHijosVisibles= new CideSprite();

            			botonXmlsHijosVisibles.crearRectangulo(180,0,20,20, 0x9999FF);
            			botonXmlsHijosVisibles.crearRectangulo(182,8,18,4, 0x000000, 0x000000);
                        botonXmlsHijosVisibles.addEventListener(MouseEvent.CLICK, xmlsHijosVisibles);
                        this.addChild(botonXmlsHijosVisibles);
		               // boton= crearBoton("+", botonesHijosVisibles);

                        //this.addChild(boton);

			            this.addChild(botonesHijos);

			            this.xmlsHijos= new CideSprite();
			            this.addChild(this.xmlsHijos);


			        }
		            boton= crearBoton(parametros.@nombre, agregarHijo);
                    boton.x=0;
                    boton.y=contadorBotones*20;
                    //boton.visible=false;
                    this.botonesHijos.addChild(boton);
                    contadorBotones++;
			    }

			}*/
		}

		public function seleccionarElemento(e:MouseEvent):void{
		    //trace("Click en "+this.nombreXML);
            this.motor.seleccionarElemento(this);

		}

		public function dibujarElemento():void{


		    var x:Number=0;
		    var y:Number=0;
		    colorFondo=0xe5e5e5;
		    trace("This: "+this.padreXML);
		    if (this.padreXML==null){
		        ancho=this.motor.parametros["ancho"]*0.8;
		        alto=this.motor.parametros["alto"]*0.8;
		        colorFondo=0xFFFFFF;
		    }


			//this.fondoContenedor = new ResizePane();
   			//this.fondo = new CideSprite();
            //fondo.move(10,10);
            //fondo.setSize(ancho,alto);

			//fondo.crearRectangulo(0,0,ancho,alto, colorFondo);
            //fondo.addEventListener(MouseEvent.CLICK, seleccionarElemento);
            
            //fondoContenedor.source=this.fondo;
            
		    //addChild(fondoContenedor);

		    


            if (this.padreXML!=null){
		        var tx:TextField;
		        var xml:XML=listaXML[nombreXML];
       			var titulo:CideSprite= new CideSprite();
			    titulo.crearRectangulo(0,0,ancho,20, 0x5f5f5f);
			    drageablePadre(titulo);
		        addChild(titulo);

       			resizeador = new CideSprite();
			    resizeador.crearRectangulo(0,0,16,16, 0x5f5f5f);
			    resizeador.x=ancho-16;
			    resizeador.y=alto-16;
			    drageable(resizeador, iniciaRedimencion, terminaRedimencion);
		        addChild(resizeador);


			    tx=crearTexto(xml.@nombre);
                tx.x=20;
                tx.y=0;
                titulo.addChild(tx);
            }
		}
		public function iniciaRedimencion(e:Event):void{
			redimencionando=true;
		}
		public function terminaRedimencion(e:Event):void{
			redimencionando=false;
		}

		public function repintar(e:Event):void{
			if (anteriorx!=resizeador.x || anteriory!=resizeador.y){
				trace(""+resizeador.x+" - "+resizeador.y);
            	trace(this.parent.x + "-" + this.parent.y);
            }
			anteriorx=resizeador.x;
			anteriory=resizeador.y;
			if(redimencionando){
				if (resizeador.x<100){
					resizeador.x=100;
					resizeador.stopDrag();
				}
				if (resizeador.y<80){
					resizeador.y=80;
					resizeador.stopDrag();
				}


/*
				if (this.mouseX>100)
					resizeador.x=this.mouseX;
				if (this.mouseY>80)
					resizeador.y=this.mouseY;*/
			
				ancho=resizeador.x+16;
				alto=resizeador.y+16;
			}
			fondo.graphics.clear();
            fondo.graphics.lineStyle(2, 0xFF0000);
            fondo.graphics.moveTo(0, 0);
            fondo.graphics.lineTo(-this.parent.x, -this.parent.y);

			//fondo.crearRectangulo(0,0,ancho,alto, colorFondo);
			//var rectanguloContenedor:Rectangle = new Rectangle(0, 0, ancho+1, alto+1);
			//this.scrollRect = rectanguloContenedor;

            
            //trace("Repintando");

		}


		public function botonesHijosVisibles(e:MouseEvent):void{
		    this.botonesHijos.visible=!this.botonesHijos.visible;
		    if (this.botonesHijos.visible){
		        this.botonesHijos.x=0;
		    }else{
    		    this.botonesHijos.x=-10000;
    		}
		}
        public function xmlsHijosVisibles(e:MouseEvent):void{
            if (this.Hijos!=null){
		        this.Hijos.visible=!this.Hijos.visible;


		        if (this.Hijos.visible){
		            this.Hijos.x=0;
           			//botonXmlsHijosVisibles.addChild(crearRectangulo(2,8,18,4, 0x000000, 0x000000));
		        }else{
        		    this.Hijos.x=-10000;
           			//botonXmlsHijosVisibles.addChild(crearRectangulo(2,4,18,16, 0x000000, 0x000000));
        		}
    		}
		}
		public function aXML(base:XML=null):String{
    		var x:CXML;
    		var xml:XML;
    		var i:Number;

    		var respuesta:String;
    		xml=this.listaXML[this.nombreXML];

            var nodoRespuesta:XML= new XML("<"+xml.@etiqueta+"/>");
            for(i=0;i<contadorPropiedades;i++){
		        nodoRespuesta[objetoNombrePropiedades[i]]=objetoPropiedades[i];
            }
		    for each(x in this.objetoHijos){
                nodoRespuesta.appendChild(x.aXML());
		    }
		    return nodoRespuesta;
		}
	}
}

