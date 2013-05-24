package ideLibrerias{
	/**
	Clase CTexto
	version 1,0
	23/10/2008
	 */
	import flash.events.*;
	public class CTexto {
		public var idTexto:Number;
		private var texto:String;
		private var preguntas:Array;
		private var idsPreguntas:Array;
		private var preguntaACargar:Number;
		private var idJuego:Number;
		private var complejidad:Number;
		private var noDistractores:Number;
		public var avisar:Boolean;
		public var centralDeEventos:EventDispatcher;
		private var com:CComunicacion;
		
		public function CTexto(idJuego:Number,complejidad:Number){
			this.idJuego=idJuego;
			this.complejidad=complejidad;
			this.preguntas=new Array();
			this.idsPreguntas=new Array();
			this.centralDeEventos=new EventDispatcher();
			this.com=new CComunicacion();
			this.idTexto=0;
			this.avisar=true;
			this.noDistractores=3;
		}
		
		public function toString(){
			var total:String="idTexto:\n\t"+this.idTexto+"\n";
			total+="Texto:\n\t"+this.texto+"\n";
			total+="Preguntas:\n\t"+this.preguntas;
			return total;
		}
		
		public function cargar(){
			this.com.EstablecerFuncionDeEventoCompletado("llegoListadoDatos",this.llegoListadoDatos);
			this.com.Consultar("SELECT idTexto,texto,idPregunta FROM (SELECT idTexto,complejidad FROM (SELECT idTexto,sum(puntos) as complejidad FROM ((`idecc_1_texto` JOIN idecc_1_textosDeUnJuego USING(idTexto)) JOIN idecc_1_valoresDeUnTexto USING(idTexto)) JOIN idecc_1_valor USING(idValor)   WHERE idJuego='"+this.idJuego+"' GROUP BY idTexto) as foo WHERE complejidad='"+this.complejidad+"' ORDER BY RAND() LIMIT 1) AS foo2 JOIN idecc_1_texto USING(idTexto) JOIN idecc_1_preguntasDeUnTexto USING(idTexto);","alejandr_admincv","acgpacgp","alejandr_ideClientes","llegoListadoDatos");
		}
		
		private function llegoListadoDatos(obj) {
			this.preguntas=new Array();
			var i:Number=0;
			var tmp:String;
			if (this.com.registros.length>0) {
				this.idTexto=this.com.registros[0][0];
				this.texto=this.com.registros[0][1];
				for (i=0; i<this.com.registros.length; i++) {
					this.idsPreguntas.push(this.com.registros[i][2]);
				}
				trace("Texto cargado.");
				this.preguntaACargar=-1;
				this.centralDeEventos.addEventListener("pregunta",this.preguntaCargada);
				this.cargarSiguientePregunta();
			}
		}
		
		private function cargarSiguientePregunta(){
			this.preguntaACargar++;
			if(this.preguntaACargar==this.idsPreguntas.length){
				if (this.avisar) {
					this.centralDeEventos.dispatchEvent(new Event("texto"));
				}
			}else{
				this.cargarPregunta(this.idsPreguntas[this.preguntaACargar]);
			}
		}
		
		private function cargarPregunta(idPregunta:Number){
			var prg:CPregunta=new CPregunta();
			var i:Number=this.preguntas.push(prg);
			this.preguntas[i-1].centralDeEventos=this.centralDeEventos;
			this.preguntas[i-1].cargar(idPregunta,this.noDistractores);
		}
		
		private function preguntaCargada(obj){
			trace("Pregunta "+(this.preguntaACargar+1)+" de "+this.idsPreguntas.length);
			this.cargarSiguientePregunta();
		}
		
	}
}
		