<?php

	class CUnionLogica implements Iterator{
	
		private $posicion = 0;
		private $llaves=array();
		private $tipo=Y;
		private $hijos=array();
		
		function __construct($tipo=null, $hijos=null){
			$this->posicion = 0;
			if(!is_null($tipo)){
				$this->setTipo($tipo);
			}
			if(!is_null($hijos)){
				$this->setHijos($hijos);
			}
		}

		public function setTipo($tipo){
			if(is_int($tipo)){
				if($tipo==Y || $tipo==O){
					$this->tipo=$tipo;
				}else{
					throw new tipoInvalido(t(__METHOD__.": Tipo ".$tipo." invalido."));
				}
			}else{
				throw new tipoInvalido(t(__METHOD__.": Tipo ".$tipo." invalido."));
			}
		}
		public function setHijos($hijos){
			if(is_array($hijos)){
				if(count($hijos)>0){
					$this->hijos=$hijos;
					$this->llaves=array_keys($this->hijos);
				}
			}else{
				throw new tipoInvalido(t(__METHOD__.": El parámetro hijos debe ser un Array."));
			}
		}
		public function add($hijo,$nombre=null){
			if(!is_null($hijo)){
				if(is_null($nombre)>0){
					$this->hijos[]=$hijo;
					$this->llaves=array_keys($this->hijos);
				}else{
					if(is_string($nombre)){
						$this->hijos[$nombre]=$hijo;
						$this->llaves=array_keys($this->hijos);
					}else{
						throw new tipoInvalido(t(__METHOD__.": El parámetro nombre debe ser de tipo String."));
					}
				}
			}else{
				throw new tipoInvalido(t(__METHOD__.": El método add espera un parámetro no nulo."));
			}
		}
		public function remove($id){
			if(isset($this->hijos[$id])){
				unset($this->hijos[$id]);
				$this->llaves=array_keys($this->hijos);
			}
		}
		
		public function getTipo(){
			return $this->tipo;
		}
		public function getHijos(){
			return $this->hijos;
		}
		public function getHijo($id){
			if(isset($this->hijos[$id])){
				return $this->hijos[$id];
			}
			return "";
		}

		function rewind() {
			$this->posicion = 0;
		}

		function current() {
			return $this->hijos[$this->llaves[$this->posicion]];
		}

		function key() {
			return $this->llaves[$this->posicion];
		}

		function next() {
			++$this->posicion;
		}

		function valid() {
			if(isset($this->llaves[$this->posicion])){
				return isset($this->hijos[$this->llaves[$this->posicion]]);
			}else{
				return false;
			}
		}

		public function __toString(){
			$t =implode(" ".CConstantes::codToString($this->tipo)." ",$this->hijos);
			return $t;
		}
	}
?>
