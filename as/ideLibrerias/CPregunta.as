package ideLibrerias{
	/**
	Clase CPregunta
	version 1,0
	23/10/2008
	 */
	import flash.events.*;
	public class CPregunta {
		public var pregunta:String;
		public var listo:Boolean;
		public var avisar:Boolean;
		public var centralDeEventos:EventDispatcher;
		public var idPregunta:Number;
		private var respuestaCorrecta:String;
		private var distractores:Array;
		private var noDistractores:Number;
		private var com:CComunicacion;
		
		public function CPregunta() {
			this.listo=false;
			this.avisar=true;
			this.idPregunta=0;
			this.com=new CComunicacion();
			this.centralDeEventos=new EventDispatcher();
		}
		
		public function cargar(idPregunta:Number,noDistractores:Number){
			this.idPregunta=idPregunta;
			this.noDistractores=noDistractores;
			this.com.EstablecerFuncionDeEventoCompletado("llegoListadoDatos",llegoListadoDatos);
			this.com.Consultar("SELECT pregunta FROM `idecc_1_pregunta` WHERE `idPregunta`='"+idPregunta+"' UNION SELECT respuestaCorrecta FROM `idecc_1_pregunta` WHERE `idPregunta`='"+idPregunta+"' UNION SELECT * FROM (SELECT distractor FROM `idecc_1_pregunta` JOIN idecc_1_distractoresDeUnaPregunta USING(idPregunta) JOIN idecc_1_distractor USING(idDistractor) WHERE `idPregunta`='"+idPregunta+"' ORDER BY RAND() LIMIT "+(noDistractores+1)+") AS foo;","alejandr_admincv","acgpacgp","alejandr_ideClientes","llegoListadoDatos");
		}
		
		public function llegoListadoDatos(obj) {
			this.distractores=new Array();
			var i:Number=0;
			var tmp:String;
			if (this.com.registros.length>0) {
				this.pregunta=this.com.registros[0][0];
				this.respuestaCorrecta=this.com.registros[1][0];
				for (i=0; i<this.noDistractores; i++) {
					this.distractores.push(this.com.registros[2+i][0]);
				}
			}
			if (this.avisar) {
				this.centralDeEventos.dispatchEvent(new Event("pregunta"));
			}
			this.listo=true;
		}
		
		public function toString(){
			return "¿"+this.pregunta+"?:"+this.respuestaCorrecta+":"+this.distractores;
		}
	}
}