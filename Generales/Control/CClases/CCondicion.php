<?php
	class CCondicion{
		private $tipo=BD;
		private $campo="";
		private $tabla="";
		private $operacion=201;
		private $valor;
		function __construct($tipo=null, $tabla=null, $campo=null, $operacion=null,$valor=null){
			if(!is_null($tipo)){$this->setTipo($tipo);}
			if(!is_null($campo)){$this->setCampo($campo);}
			if(!is_null($tabla)){$this->setTabla($tabla);}
			if(!is_null($operacion)){$this->setOperacion($operacion);}
			if(!is_null($valor)){$this->setValor($valor);}
		}
		public function setTipo($tipo){
			if(is_int($tipo)){
				if($tipo>=101 && $tipo<=101){
					$this->tipo=$tipo;
				}else{
					throw new tipoInvalido(t("CCondicion: Tipo ".CConstantes::codToString($tipo)." invalido."));
				}
			}else{
				throw new tipoInvalido(t("CCondicion: Tipo ".CConstantes::codToString($tipo)." invalido."));
			}
		}
		public function setCampo($campo){
			if(is_string($campo)){
				if(strlen($campo)>0){
					$this->campo=$campo;
				}else{
					throw new tipoInvalido(t("CCondicion: Propiedad campo no puede ser establecida como vacía."));
				}
			}else{
				throw new tipoInvalido(t("CCondicion: Propiedad campo es de tipo Cadena."));
			}
		}
		public function setTabla($tabla){
			if(is_string($tabla)){
				if(strlen($tabla)>0){
					$this->tabla=$tabla;
				}else{
					throw new tipoInvalido(t("CCondicion: Propiedad tabla no puede ser establecida como vacía."));
				}
			}else{
				throw new tipoInvalido(t("CCondicion: Propiedad tabla es de tipo Cadena."));
			}
		}
		public function setOperacion($operacion){
			if(is_int($operacion)){
				if($operacion>=201 && $operacion<=207){
					$this->operacion=$operacion;
				}else{
					throw new tipoInvalido(t("CCondicion: Operacion ".CConstantes::codToString($operacion)." invalido."));
				}
			}else{
				throw new tipoInvalido(t("CCondicion: Operacion ".CConstantes::codToString($operacion)." invalido."));
			}
		}
		public function setValor($valor){
			$this->valor=$valor;
		}
		public function getTipo(){
			return $this->tipo;
		}
		public function getCampo(){
			return $this->campo;
		}
		public function getTabla(){
			return $this->tabla;
		}
		public function getOperacion(){
			return $this->operacion;
		}
		public function getValor(){
			return $this->valor;
		}
		public function __toString(){
			$t =CConstantes::codToString($this->tipo).": ";
			$t.=$this->tabla.".";
			$t.=$this->campo."";
			$t.=CConstantes::codToString($this->operacion)."";
			$t.=$this->valor;
			return $t;
		}
	}
?>
