<?php
	class VO0CasoUso{
		protected $idCasoUso=null;
		protected $idPaquete=null;
		protected $nombreCasoUso=null;
		function VO0CasoUso($idCasoUso=null,$idPaquete=null,$nombreCasoUso=null){
			if(!is_null($idCasoUso)) $this->setIdCasoUso($idCasoUso);
			if(!is_null($idPaquete)) $this->setIdPaquete($idPaquete);
			if(!is_null($nombreCasoUso)) $this->setNombreCasoUso($nombreCasoUso);
		}
		function toString(){
			return 
			'idCasoUso='.$this->idCasoUso.', '.
			'idPaquete='.$this->idPaquete.', '.
			'nombreCasoUso='.$this->nombreCasoUso.', ';
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
			return json_encode(array('idCasoUso'=>$this->idCasoUso,'idPaquete'=>$this->idPaquete,'nombreCasoUso'=>$this->nombreCasoUso));
		}
		function setIdCasoUso($idCasoUso){ 
 			if(is_null($idCasoUso)||strlen($idCasoUso)==0){ 
				 throw new valorNuloInvalido('El campo idCasoUso es requerido.');
			 }
 			$this->idCasoUso=$idCasoUso; 
		}
		function setIdPaquete($idPaquete){ 
 			if(is_null($idPaquete)||strlen($idPaquete)==0){ 
				 throw new valorNuloInvalido('El campo idPaquete es requerido.');
			 }
 			$this->idPaquete=$idPaquete; 
		}
		function setNombreCasoUso($nombreCasoUso){ 
 			if(is_null($nombreCasoUso)||strlen($nombreCasoUso)==0){ 
				 throw new valorNuloInvalido('El campo nombreCasoUso es requerido.');
			 }
 			$this->nombreCasoUso=$nombreCasoUso; 
		}
		function getIdCasoUso(){ return $this->idCasoUso; }
		function getIdPaquete(){ return $this->idPaquete; }
		function getNombreCasoUso(){ return $this->nombreCasoUso; }
	}
?>