<?php
	class VO0UsuarioCasoUso{
		protected $idCasoUso=null;
		protected $idUsuario=null;
		protected $condiciones=null;
		function VO0UsuarioCasoUso($idCasoUso=null,$idUsuario=null,$condiciones=null){
			if(!is_null($idCasoUso)) $this->setIdCasoUso($idCasoUso);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($condiciones)) $this->setCondiciones($condiciones);
		}
		function toString(){
			return 
			'idCasoUso='.$this->idCasoUso.', '.
			'idUsuario='.$this->idUsuario.', '.
			'condiciones='.$this->condiciones.', ';
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
			return json_encode(array('idCasoUso'=>$this->idCasoUso,'idUsuario'=>$this->idUsuario,'condiciones'=>$this->condiciones));
		}
		function setIdCasoUso($idCasoUso){ 
 			if(is_null($idCasoUso)||strlen($idCasoUso)==0){ 
				 throw new valorNuloInvalido('El campo idCasoUso es requerido.');
			 }
 			$this->idCasoUso=$idCasoUso; 
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setCondiciones($condiciones=null){ 
  			$this->condiciones=$condiciones; 
		}
		function getIdCasoUso(){ return $this->idCasoUso; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getCondiciones(){ return $this->condiciones; }
	}
?>