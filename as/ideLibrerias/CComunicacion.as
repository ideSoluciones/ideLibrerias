package ideLibrerias{
	import flash.display.*;
	import flash.events.*;
	import flash.net.*;
	public class CComunicacion extends Sprite {
		public var mensajes:Array;
		private var servidor:String;
		private var metodo:String;
		public var registros:Array;
		private var centralDeCargaYEventos:URLLoader;

		public function CComunicacion() {
			this.servidor = "http://idesoluciones.com/servidor.php";
			this.metodo = "POST";
			this.registros = new Array();
			this.centralDeCargaYEventos = new URLLoader();
			this.mensajes=new Array();
		}

		private function configurarEscuchadores(centralDeEventos:IEventDispatcher):void {
			centralDeEventos.addEventListener(Event.COMPLETE, ManipuladorDeEventoCompletado);
			centralDeEventos.addEventListener(Event.OPEN, ManipuladorDeEventoAbrir);
			centralDeEventos.addEventListener(ProgressEvent.PROGRESS, ManipuladorDeEventoProgreso);
			centralDeEventos.addEventListener(SecurityErrorEvent.SECURITY_ERROR, ManipuladorDeEventoErrorDeSeguridad);
			centralDeEventos.addEventListener(HTTPStatusEvent.HTTP_STATUS, ManipuladorDeEventoEstadoHttp);
			centralDeEventos.addEventListener(IOErrorEvent.IO_ERROR, ManipuladorDeEventoErrorDeEntradaSalida);
		}

		private function ManipuladorDeEventoCompletado(event:Event):void {
			var loader:URLLoader = URLLoader(event.target);
			if (loader.data.estatus == "ok") {
				var resp:String=loader.data.registros;
				var arrTmp:Array;
				var i:Number=0;
				arrTmp=resp.split(":;:");
				this.registros=new Array();
				for (i=0; i<arrTmp.length; i++) {
					this.registros.push(arrTmp[i].split(".,."));
				}
				this.centralDeCargaYEventos.dispatchEvent(new Event(loader.data.funcion));
			} else {
				if (loader.data.estatus == "ok1") {
					this.centralDeCargaYEventos.dispatchEvent(new Event(loader.data.funcion));
				} else {
					this.mensajes.push("Problemas con el servidor Intentalo de Nuevo");
				}
			}
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
		private function enviar(accion:String,parametros:String,user:String,pass:String,db:String,funcion:String) {
			var peticion:URLRequest = new URLRequest(this.servidor);
			var variables:URLVariables = new URLVariables();
			this.centralDeCargaYEventos.dataFormat = URLLoaderDataFormat.VARIABLES;
			variables.accion = accion;
			variables.parametros = parametros;
			variables.user=user;
			variables.pass=pass;
			variables.db=db;
			variables.funcion=funcion;
			peticion.data = variables;
			peticion.method=this.metodo;
			configurarEscuchadores(this.centralDeCargaYEventos);
			try {
				this.centralDeCargaYEventos.load(peticion);
			} catch (error:Error) {
				this.mensajes.push("Problemas con el servidor Intentalo de Nuevo");
				throw new Error("Problemas con el servidor Intentalo de Nuevo");
			}
		}
		public function Consultar(sql:String,user:String,pass:String,db:String,funcion:String) {
			enviar("Consultar",sql,user,pass,db,funcion);
		}
		public function Insertar(sql:String,user:String,pass:String,db:String,funcion:String) {
			enviar("Insertar",sql,user,pass,db,funcion);
		}
		public function EstablecerFuncionDeEventoCompletado(nombre:String,funcion:Function){
			this.centralDeCargaYEventos.addEventListener(nombre,funcion);
		}
	}
}