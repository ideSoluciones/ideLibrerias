<?php
	class VO0Rol{
		protected $idRol=null;
		protected $nombreRol=null;
		function VO0Rol($idRol=null,$nombreRol=null){
			if(!is_null($idRol)) $this->setIdRol($idRol);
			if(!is_null($nombreRol)) $this->setNombreRol($nombreRol);
		}
		function toString(){
			return 
			'idRol='.$this->idRol.', '.
			'nombreRol='.$this->nombreRol.', ';
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
			return json_encode(array('idRol'=>$this->idRol,'nombreRol'=>$this->nombreRol));
		}
		function setIdRol($idRol){ 
 			if(is_null($idRol)||strlen($idRol)==0){ 
				 throw new valorNuloInvalido('El campo idRol es requerido.');
			 }
 			$this->idRol=$idRol; 
		}
		function setNombreRol($nombreRol){ 
 			if(is_null($nombreRol)||strlen($nombreRol)==0){ 
				 throw new valorNuloInvalido('El campo nombreRol es requerido.');
			 }
 			$this->nombreRol=$nombreRol; 
		}
		function getIdRol(){ return $this->idRol; }
		function getNombreRol(){ return $this->nombreRol; }
	}
?>