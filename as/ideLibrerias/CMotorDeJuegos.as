package ideLibrerias{
	/**
	Clase CMotorDeJuegos
	version 1,0
	23/10/2008
	 */
	import flash.events.*;
	public class CMotorDeJuegos {
		public var centralDeEventos:EventDispatcher;
		private var valXmlJuego:XML;
		private var variables:Array;
		private var idJuego:Number;
		private var sentencia:Number;
		private var xmlConfiguracion:XML;
		private var configuracion:Number;
		private var com:CComunicacion;
		private var complejidadesParaJuego:Array;
		private var complejidadActual:Number;
		private var noResCoAnSigNivel:Number;
		private var variableActualTextoActivo:String;
		private var textoActual:CTexto;

		public function CMotorDeJuegos() {
			this.complejidadActual=0;
			this.noResCoAnSigNivel=20;
			this.sentencia=-1;
			this.variables= new Array();
			this.complejidadesParaJuego=new Array();
			this.com=new CComunicacion();
			this.xmlConfiguracion=new XML();
			this.centralDeEventos=new EventDispatcher();
		}
		public function obtenerXmlDeUnTexto(strXmlJuego:String) {
			this.xmlJuego=new XML(strXmlJuego);
		}
		public function set xmlJuego(val:XML) {
			this.valXmlJuego=val;
		}
		public function get xmlJuego() {
			return this.valXmlJuego;
		}
		public function ejecutar() {
			this.evaluarSiguienteSentencia();
		}
		public function toString() {
			var total:String="";
			total+="Variables\n\t"+this.variables+"\n";
			total+="idJuego\n\t"+this.idJuego+"\n";
			total+="Lista de Complejidades\n\t"+this.complejidadesParaJuego+"\n";
			total+="No de respuestas correctas para avanzar nivel\n\t"+this.noResCoAnSigNivel+"\n";
			total+="No de respuestas correctas para avanzar nivel\n\t"+this.noResCoAnSigNivel+"\n";
			total+=this.textoActual.toString();
			return total;
		}
		private function evaluarSiguienteSentencia() {
			this.sentencia++;
			trace("Sentencia "+this.sentencia);
			if (this.xmlJuego.children().length()>this.sentencia && this.xmlJuego.children().length()>0) {
				this.evaluarSentencia(this.sentencia);
			} else {
				trace("Finalizado...");
				trace(this.toString());
			}
		}
		private function evaluarSentencia(sentencia:Number) {
			switch (this.xmlJuego.children()[sentencia].name().localName) {
				case "Variable" :
					this.evaluarVariable(this.xmlJuego.children()[sentencia].attribute("nombre"),this.xmlJuego.children()[sentencia].attribute("valor"),this.xmlJuego.children()[sentencia].attribute("tipo"));
					break;
				case "Accion" :
					this.evaluarAccion(this.xmlJuego.children()[sentencia]);
					break;
				case "Si" :
					this.evaluarSiguienteSentencia();
					break;
				case "Configurar" :
					this.evaluarConfigurar(this.xmlJuego.children()[sentencia]);
					break;
			}
		}
		private function evaluarVariable(nombre:String,valor:String,tipo:String) {
			switch (valor) {
				case "[obtenerObjetoTexto]" :
					this.evaluarObtenerObjetoTexto(nombre);
					break;
				default :
					this.variables.push(new CVariable(nombre,tipo,valor));
					this.evaluarSiguienteSentencia();
			}
		}
		private function evaluarConfigurar(config:XML) {
			this.xmlConfiguracion=config;
			this.configuracion=-1;
			evaluarSiguienteConfiguracion();
		}
		private function evaluarSiguienteConfiguracion() {
			this.configuracion++;
			if (this.xmlConfiguracion.children().length()<=this.configuracion) {
				this.evaluarSiguienteSentencia();
			} else {
				switch (this.xmlConfiguracion.children()[this.configuracion].name().localName) {
					case "Establecer" :
						this.evaluarEstablecer(this.xmlConfiguracion.children()[this.configuracion].attribute("nombre"),this.xmlConfiguracion.children()[this.configuracion].attribute("valor"));
						break;
					default :
						this.evaluarSiguienteConfiguracion();
				}
			}
		}
		private function evaluarEstablecer(nombre:String,valor:String) {
			switch (nombre) {
				case "juego" :
					this.idJuego=parseInt(valor);
					evaluarSiguienteConfiguracion();
					break;
				case "noResCoAnSigNivel" :
					this.noResCoAnSigNivel=parseInt(valor);
					evaluarSiguienteConfiguracion();
					break;
				case "complejidades" :
					switch (valor) {
						case "Auto" :
							this.com.EstablecerFuncionDeEventoCompletado("llegoListadoDatos",llegoListadoDatosConfiguracionPropiedades);
							this.com.Consultar("SELECT complejidad FROM (SELECT idTexto, sum(puntos) as complejidad FROM ((`idecc_1_texto` JOIN idecc_1_textosDeUnJuego USING(idTexto)) JOIN idecc_1_valoresDeUnTexto USING(idTexto)) JOIN idecc_1_valor USING(idValor)  WHERE idJuego='"+this.idJuego+"' GROUP BY idTexto ) AS foo GROUP BY complejidad ORDER BY complejidad ASC","alejandr_admincv","acgpacgp","alejandr_ideClientes","llegoListadoDatos");
							break;
						default :
							this.complejidadesParaJuego=valor.split(/,/);
							evaluarSiguienteConfiguracion();
					}
					break;
				default :
					this.evaluarSiguienteConfiguracion();
			}
		}
		private function llegoListadoDatosConfiguracionPropiedades(obj) {
			this.complejidadesParaJuego=this.com.registros;
			evaluarSiguienteConfiguracion();
		}
		private function evaluarObtenerObjetoTexto(variable:String) {
			this.variableActualTextoActivo=variable;
			this.textoActual=new CTexto(this.idJuego,this.complejidadesParaJuego[this.complejidadActual]);
			this.centralDeEventos.addEventListener("texto",this.textoCargado);
			this.textoActual.centralDeEventos=this.centralDeEventos;
			this.textoActual.cargar();
		}
		private function textoCargado(obj){
			this.evaluarSiguienteSentencia();
		}
		
		private function evaluarAccion(xmlAccion:XML){
			var indice:int;
			var nombre:String=xmlAccion.attribute("nombre");
			trace("Accion:"+nombre);
			switch(nombre){
				case "asignar":
					indice=this.buscarVariable(xmlAccion.attribute("variable"));
					this.variables[indice].valor=this.evaluarValor(xmlAccion.attribute("valor"));
					break;
				case "mostrar":
					switch(xmlAccion.attribute("tipo")){
						case "ventanaApuntada":
							
							break;
					}
			}
			this.evaluarSiguienteSentencia();
		}
		/**
		*	buscarVariable
		*	Busca un objeto CVariable dentro del listado de variables.
		*/
		private function buscarVariable(nombre:String):int{
			var i:int;
			var bandera:Boolean=false;
			for(i=0;i<this.variables.length;i++){
				if(this.variables[i].nombre==nombre){
					bandera=true;
					break;
				}
			}
			if(bandera){
				return i;
			}else{
				throw new Error("Variable ´´"+nombre+"´´ no encontrada");
			}
		}
		/**
		*	evaluarValor
		*	Evalua una sentencia de valor y opera si es requerido
		*/
		private function evaluarValor(valor:String){
			var a:String;
			var operandos:Array=new Array();
			var operadores:Array=new Array();
			var i:int,j:int,indice:int;
			var tmp;
			trace(valor);
			for(i=0;i<valor.length;i++){
				switch(valor.charAt(i)){
					case "{":
						var buscaCierre:Boolean=false;
						for(j=i+1;j<valor.length;j++){
							if(valor.charAt(j)=="}"){
								buscaCierre=true;
								break;
							}
						}
						if(!buscaCierre){
							throw new Error("Falta cierre } en "+valor);
						}
						indice=buscarVariable(valor.substr(i+1,j-i-1));
						tmp=this.variables[indice].valor;
						i=j;
						break;
					case " ":
					case "}":
						break;
					case "+":
						operadores.push("+");
						operandos.push(tmp);
						tmp="";
						break;
					default:
						tmp+=valor.charAt(i);
				}
			}
			operandos.push(tmp);
			for(i=0;i<operadores.length;i++){
				if(operandos.length>1){
					switch(operadores[i]){
						case "+":
						default:
							if(!isNaN(parseFloat(operandos[0])) && !isNaN(parseFloat(operandos[1]))){
								operandos[0]=parseFloat(operandos[0]);
								operandos[1]=parseFloat(operandos[1]);
							}
							operandos[1]=operandos[0]+operandos[1];
							operandos=operandos.slice(1,operandos.length);
					}
				}
			}
			return operandos[0];
		}
	}
}