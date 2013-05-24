<?php
	class VO0Paquete{
		protected $idPaquete=null;
		protected $nombrePaquete=null;
		function VO0Paquete($idPaquete=null,$nombrePaquete=null){
			if(!is_null($idPaquete)) $this->setIdPaquete($idPaquete);
			if(!is_null($nombrePaquete)) $this->setNombrePaquete($nombrePaquete);
		}
		function toString(){
			return 
			'idPaquete='.$this->idPaquete.', '.
			'nombrePaquete='.$this->nombrePaquete.', ';
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
			return json_encode(array('idPaquete'=>$this->idPaquete,'nombrePaquete'=>$this->nombrePaquete));
		}
		function setIdPaquete($idPaquete){ 
 			if(is_null($idPaquete)||strlen($idPaquete)==0){ 
				 throw new valorNuloInvalido('El campo idPaquete es requerido.');
			 }
 			$this->idPaquete=$idPaquete; 
		}
		function setNombrePaquete($nombrePaquete){ 
 			if(is_null($nombrePaquete)||strlen($nombrePaquete)==0){ 
				 throw new valorNuloInvalido('El campo nombrePaquete es requerido.');
			 }
 			$this->nombrePaquete=$nombrePaquete; 
		}
		function getIdPaquete(){ return $this->idPaquete; }
		function getNombrePaquete(){ return $this->nombrePaquete; }
	}
?>