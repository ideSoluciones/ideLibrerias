<?php
	class VO0RolCasoUso{
		protected $idRol=null;
		protected $idCasoUso=null;
		protected $condiciones=null;
		function VO0RolCasoUso($idRol=null,$idCasoUso=null,$condiciones=null){
			if(!is_null($idRol)) $this->setIdRol($idRol);
			if(!is_null($idCasoUso)) $this->setIdCasoUso($idCasoUso);
			if(!is_null($condiciones)) $this->setCondiciones($condiciones);
		}
		function toString(){
			return 
			'idRol='.$this->idRol.', '.
			'idCasoUso='.$this->idCasoUso.', '.
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
			return json_encode(array('idRol'=>$this->idRol,'idCasoUso'=>$this->idCasoUso,'condiciones'=>$this->condiciones));
		}
		function setIdRol($idRol){ 
 			if(is_null($idRol)||strlen($idRol)==0){ 
				 throw new valorNuloInvalido('El campo idRol es requerido.');
			 }
 			$this->idRol=$idRol; 
		}
		function setIdCasoUso($idCasoUso){ 
 			if(is_null($idCasoUso)||strlen($idCasoUso)==0){ 
				 throw new valorNuloInvalido('El campo idCasoUso es requerido.');
			 }
 			$this->idCasoUso=$idCasoUso; 
		}
		function setCondiciones($condiciones=null){ 
  			$this->condiciones=$condiciones; 
		}
		function getIdRol(){ return $this->idRol; }
		function getIdCasoUso(){ return $this->idCasoUso; }
		function getCondiciones(){ return $this->condiciones; }
	}
?>