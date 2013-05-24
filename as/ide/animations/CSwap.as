package ide.animations{
	
	//import fl.motion.Animator;
	import flash.filters.*;
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	import caurina.transitions.Tweener;
	import ide.display.*;

	
	
	public class CSwap {
		private var nodo:XML;
		private var objeto:Object;
		
		private var transicionEntra:String;
		private var transicionSale:String;
		
		public function CSwap(_nodo:XML, _objeto:Object){
			this.nodo=_nodo;
			this.objeto=_objeto;
			
		}
		public function animacion():void{
			//trace("Estoy en el inicio de la animacion");
			if(valido(nodo.@xInicio)){
				objeto.x = nodo.@xInicio;
			}else{
				objeto.x=0;
			}
			if(valido(nodo.@yInicio)){
				objeto.y = nodo.@yInicio;
			}else{
				objeto.y=0;
			}
			var xFinal:Number=500;
			if(valido(nodo.@xFinal)){
				xFinal=nodo.@xFinal;
			}
			var yFinal:Number=0;
			if(valido(nodo.@yFinal)){
				yFinal=nodo.@yFinal;
			}
			var tiempo:Number=5;
			if(valido(nodo.@tiempo)){
				tiempo=nodo.@tiempo;
			}
			transicionEntra="easeOutInBack";
			if(valido(nodo.@transicionEntra)){
				transicionEntra=nodo.@transicionEntra;
			}
			transicionSale="easeOutInBack";
			if(valido(nodo.@transicionSale)){
				transicionSale=nodo.@transicionSale;
			}
			

		}
		
		public function cuadroEntra():void{ 
		var xFinal:Number=0;
		var yFinal:Number=0;
		var tiempo:Number=10;
		
			Tweener.addTween
				(
					objeto,
					{
						x:xFinal,
						y:yFinal,
						time:tiempo,
						transition:transicionEntra, 
						onComplete:function():void {/*
							trace("Termino "+this);
							//this.animacion();
							trace("RootC1:"+this.parent);
							trace("RootC2:"+this.parent.parent.parent);
							trace("RootC3:"+this.parent.parent.parent.parent);
							trace("RootC4:"+this.parent.parent.parent.parent.parent);
							trace("Llamando de nuevo la animación");*/
							this.propiedades.apuntador.animacion();
						}
					}
				);
		}
		
		private function valido(algo:Object):Boolean{
			var s:String=algo.toString();
			if (s.length>0){
				return true;
			}
			return false;
		}		
	}
}
