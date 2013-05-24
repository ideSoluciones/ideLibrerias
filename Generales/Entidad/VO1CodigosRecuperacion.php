<?php
	class VO1CodigosRecuperacion{
		protected $idCodigoRecuperacion=null;
		protected $idUsuario=null;
		protected $codigo=null;
		protected $caducidad=null;
		function VO1CodigosRecuperacion($idCodigoRecuperacion=null,$idUsuario=null,$codigo=null,$caducidad=null){
			if(!is_null($idCodigoRecuperacion)) $this->setIdCodigoRecuperacion($idCodigoRecuperacion);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($codigo)) $this->setCodigo($codigo);
			if(!is_null($caducidad)) $this->setCaducidad($caducidad);
		}
		function toString(){
			return 
			'idCodigoRecuperacion='.$this->idCodigoRecuperacion.', '.
			'idUsuario='.$this->idUsuario.', '.
			'codigo='.$this->codigo.', '.
			'caducidad='.$this->caducidad.', ';
		}
		function set($parametros){
			if(is_array($parametros)){
				foreach($parametros as $nombre=>$valor){
					$funcion='set'.ucfirst($nombre);
					if(method_exists($this,$funcion)){
						$this->$funcion($valor);
					}
				}
			}
		}
		function toJson(){
			return json_encode(array('idCodigoRecuperacion'=>$this->idCodigoRecuperacion,'idUsuario'=>$this->idUsuario,'codigo'=>$this->codigo,'caducidad'=>$this->caducidad));
		}
		function setIdCodigoRecuperacion($idCodigoRecuperacion){ 
 			if(is_null($idCodigoRecuperacion)||strlen($idCodigoRecuperacion)==0){ 
				 throw new valorNuloInvalido('El campo idCodigoRecuperacion es requerido.');
			 }
 			$this->idCodigoRecuperacion=$idCodigoRecuperacion; 
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setCodigo($codigo){ 
 			if(is_null($codigo)||strlen($codigo)==0){ 
				 throw new valorNuloInvalido('El campo codigo es requerido.');
			 }
 			$this->codigo=$codigo; 
		}
		function setCaducidad($caducidad){ 
 			if(is_null($caducidad)||strlen($caducidad)==0){ 
				 throw new valorNuloInvalido('El campo caducidad es requerido.');
			 }
 			$this->caducidad=$caducidad; 
		}
		function getIdCodigoRecuperacion(){ return $this->idCodigoRecuperacion; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getCodigo(){ return $this->codigo; }
		function getCaducidad(){ return $this->caducidad; }
	}
?>