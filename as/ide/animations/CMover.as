package ide.animations{
	
	//import fl.motion.Animator;
	import flash.filters.*;
	import flash.display.*;
	import flash.text.*;
	import flash.net.*;
	import caurina.transitions.Tweener;

	
	
	public class CMover {
		private var nodo:XML;
		private var objeto:Object;
		public function CMover(_nodo:XML, _objeto:Object){
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
				xFinal=nodo.@yFinal;
			}
			var tiempo:Number=10;
			if(valido(nodo.@tiempo)){
				xFinal=nodo.@tiempo;
			}
			var transicion:String="lineal";
			if(valido(nodo.@transicion)){
				xFinal=nodo.@transicion;
			}
			
			Tweener.addTween
				(
					objeto,
					{
						x:xFinal,
						y:yFinal,
						time:tiempo,
						transition:transicion, 
						onComplete:function  ():void {
							this.animacion();
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
