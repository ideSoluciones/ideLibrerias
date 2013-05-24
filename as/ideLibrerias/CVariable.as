package ideLibrerias{
	/**
	Clase CVariable
	version 1,0
	23/10/2008
	 */
	public class CVariable {
		private var nombreVar:String;
		private var tipoVar:String;
		private var valorTexto:String;
		private var valorNumerico:Number;
		public function CVariable(nombre:String,tipo:String,valor:String) {
			this.nombre=nombre;
			this.tipo=tipo;
			this.valor=valor;
		}
		public function cargarDesdeTexto(variable:String) {
			var tmp:String;
			tmp=variable;
			var a=tmp.search(/:/);
			var b=tmp.search(/=/);
			if(a==-1){
				throw new Error("Error de asignación de variable nueva, falta el tipo.");
			}
			if(b==-1){
				throw new Error("Error de asignación de variable nueva, falta el valor.");
			}
			this.nombre=tmp.substring(0,a);
			this.tipo=tmp.substring(a+1,b);
			this.valor=tmp.substring(b+1,tmp.length);
		}
		public function get nombre(){
			return this.nombreVar;
		}
		public function get tipo(){
			return this.tipoVar;
		}
		public function set nombre(val){
			this.nombreVar=String(val);
		}
		public function set tipo(val){
			switch (val) {
				case "Numerico" :
				case "Texto" :
					this.tipoVar=String(val);
					break;
				default :
					throw new Error("Tipo '" + val + "' invalido de variable.");
			}
		}
		public function set valor(val) {
			switch (this.tipo) {
				case "Numerico" :
					this.valorNumerico=parseFloat(val);
					break;
				case "Texto" :
					this.valorTexto=String(val);
					break;
				default :
					throw new Error("Tipo '" + this.tipo + "' invalido de variable.");
			}
		}
		public function get valor() {
			switch (this.tipo) {
				case "Numerico" :
					return this.valorNumerico;
					break;
				case "Texto" :
					return this.valorTexto;
					break;
			}
		}
		public function toString(){
			return this.nombre+":"+this.tipo+"="+this.valor;
		}
	}
}