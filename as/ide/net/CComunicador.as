package ide.net{
	import flash.display.*;
	import flash.events.*;
	import flash.net.*;
	public class CComunicador extends Sprite{
		public var mensajes:Array;
		private var servidor:String;
		private var metodo:String;
		public var registros:Array;
		private var centralDeCargaYEventos:URLLoader;
		
		public var padre:Object;

		public function CComunicador(_padre:Object, nombreProyecto: String, direccionBase:String="http://192.168.1.34/ide/", direccionPedidos:String="q=1") {
			this.padre=_padre;
			this.servidor = direccionBase+direccionPedidos;
			trace("Enviando a: "+this.servidor);
			this.metodo = "POST";
			this.registros = new Array();
			this.centralDeCargaYEventos = new URLLoader();
			this.mensajes=new Array();
		}
		public function setServidor(direccion:String):void{
			this.servidor=direccion;
		}
		public function setMetodo(metodo:String):void{
			this.metodo=metodo;
		}
		public function setPadre(_padre:Object):void{
			this.padre=_padre;
		}
		public function saluda():void{
			trace("hola yo soy CComunicador");
		}
		
		public function sendData(funcion:String, _vars:URLVariables):void {  		
			var peticion:URLRequest = new URLRequest(this.servidor);  
			trace("    Enviando "+funcion);
			var loader:URLLoader = new URLLoader();  
			_vars.operacion=funcion;
			peticion.data = _vars;
			peticion.method = URLRequestMethod.POST;  
			this.centralDeCargaYEventos.dataFormat = URLLoaderDataFormat.TEXT;  
			this.configurarEscuchadores(this.centralDeCargaYEventos);
			//trace("configurando respuesta");
			//this.EstablecerFuncionDeEventoCompletado(funcion, padre.funcion);
			//trace("Enviando: "+_vars);
			try {
				this.centralDeCargaYEventos.load(peticion);
				//trace("Enviado");
			} catch (error:Error) {
				this.mensajes.push("Problemas con el servidor Intentalo de Nuevo");
				throw new Error("Problemas con el servidor Intentalo de Nuevo");
				trace("Problemas con el servidor Intentalo de Nuevo");
			}
		} 
		
		private function configurarEscuchadores(centralDeEventos:IEventDispatcher):void {
			centralDeEventos.addEventListener(Event.COMPLETE, ManipuladorDeEventoCompletado);
			centralDeEventos.addEventListener(Event.OPEN, ManipuladorDeEventoAbrir);
			centralDeEventos.addEventListener(ProgressEvent.PROGRESS, ManipuladorDeEventoProgreso);
			centralDeEventos.addEventListener(SecurityErrorEvent.SECURITY_ERROR, ManipuladorDeEventoErrorDeSeguridad);
			centralDeEventos.addEventListener(HTTPStatusEvent.HTTP_STATUS, ManipuladorDeEventoEstadoHttp);
			centralDeEventos.addEventListener(IOErrorEvent.IO_ERROR, ManipuladorDeEventoErrorDeEntradaSalida);
			//trace("Eventos basicos controlados");
		}
		
		////////////////////////////////////////////////////////////////////////
		private function ManipuladorDeEventoCompletado(event:Event):void {
			//trace(" * ManipuladorDeEventoCompletado");
			var loader:URLLoader = URLLoader(event.target);
			var respuesta:XML;
			var e:Error;
			
			
			
			try{
				//trace("Los datos son: "+loader.data+"\n\n");
				
				respuesta = new XML(loader.data);
				//trace("La respuesta es: "+respuesta);
				/*
				for each(var nodo:XML in respuesta.elements()){
					trace("Nodo: "+nodo.name());
				}
				*/			
				
				//trace("La respuesta inicia con: ["+respuesta.name()+","+respuesta+"]");
				
				/*
				if (this.centralDeCargaYEventos.hasEventListener(respuesta.name()))
					trace("El evento "+respuesta.name()+" si existe");
				else 
					trace("El evento "+respuesta.name()+" NO existe");
					*/
					
				//trace("-1-----------------------");
				//trace(respuesta);
				//trace("-2-----------------------");
				
				//trace("-3-----------------------");
				trace("Fue llamada la función: "+respuesta.name());
				if (respuesta.name()=="html" || respuesta.name()=="http://www.w3.org/1999/xhtml::html"){
					trace(respuesta);
				}
				if (respuesta.name()=="http://www.w3.org/1999/xhtml::html"){
					this.login();
				}else{
					this.centralDeCargaYEventos.dispatchEvent(new Event(respuesta.name()));
				}
			}catch(e:TypeError){
				trace("Error "+e.errorID);
				if (e.errorID==1088){
					trace(" * Logeando "+e+"\n\n"+loader.data);
					/*
					var comunicarse:CComunicador= new CComunicador();
					"http://192.168.1.33/~jag2kn/svn/ideProyectos/ASTrading/?q=18"
					*/
					
					this.login();
				}else{
					trace("ERROR - "+e+"["+loader.data+"]");
				}
			}
		}
		
		private function login():void{
			var myLoader:URLLoader = new URLLoader();
			trace(" * 1");
			myLoader.dataFormat = URLLoaderDataFormat.TEXT;
			trace(" * 2");
			myLoader.load(new URLRequest("http://192.168.1.34/ide/VET/?q=1"))
			trace(" * 3");
			myLoader.addEventListener(Event.COMPLETE, onLoad);
			trace(" * 4");
			function onLoad(ev:Event):void{
				trace(" * * Carga de login");
				var loader:URLLoader = URLLoader(ev.target);
				trace(" * * Login respuesta: "+loader.data);
				
				var myLoader1:URLLoader = new URLLoader();
				trace(" * * 1");
				myLoader1.dataFormat = URLLoaderDataFormat.TEXT;
				trace(" * * 2");
				myLoader1.load(new URLRequest("http://192.168.1.34/ide/VET/?q=1/6&Usuario=vendedor1&Pass=Vendedor1!"))
				trace(" * * 3");
				myLoader1.addEventListener(Event.COMPLETE, onLoad1);
				trace(" * * 4");
			}
			trace(" * 5");
			function onLoad1(ev:Event):void{
				trace(" * * Login OK");
				var loader:URLLoader = URLLoader(ev.target);
				trace(" * * * Login respuesta: "+loader.data);
			}
			trace(" * 6");			
		}

		private function ManipuladorDeEventoAbrir(event:Event):void {
			this.mensajes.push("ManipuladorDeEventoAbrir: " + event);
		}

		private function ManipuladorDeEventoProgreso(event:ProgressEvent):void {
			this.mensajes.push("ManipuladorDeEventoProgreso: Bytes Cargados=" + event.bytesLoaded + " total Bytes: " + event.bytesTotal);
		}

		private function ManipuladorDeEventoErrorDeSeguridad(event:SecurityErrorEvent):void {
			this.mensajes.push("ManipuladorDeEventoErrorDeSeguridad: " + event);
			throw new Error("ManipuladorDeEventoErrorDeSeguridad: " + event);
		}

		private function ManipuladorDeEventoEstadoHttp(event:HTTPStatusEvent):void {
			this.mensajes.push("ManipuladorDeEventoEstadoHttp: " + event);
		}

		private function ManipuladorDeEventoErrorDeEntradaSalida(event:IOErrorEvent):void {
			this.mensajes.push("ManipuladorDeEventoErrorDeEntradaSalida: " + event);
			throw new Error("ManipuladorDeEventoErrorDeEntradaSalida: " + event);
		}		
		public function EstablecerFuncionDeEventoCompletado(nombre:String,funcion:Function):void{
			this.centralDeCargaYEventos.addEventListener(nombre,funcion);
			//trace("EstablecerFuncionDeEventoCompletado: "+nombre+", "+funcion);
		}
		
		/*
		public function toString():String{
			return "hola CComunicador";
		}
		*/
		/*
		public function trace(s:Object):void{
			padre.trace(s);
		}*/
	}
}
