<?php
	class VO0UsuarioRol{
		protected $idUsuario=null;
		protected $idRol=null;
		function VO0UsuarioRol($idUsuario=null,$idRol=null){
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($idRol)) $this->setIdRol($idRol);
		}
		function toString(){
			return 
			'idUsuario='.$this->idUsuario.', '.
			'idRol='.$this->idRol.', ';
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
			return json_encode(array('idUsuario'=>$this->idUsuario,'idRol'=>$this->idRol));
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setIdRol($idRol){ 
 			if(is_null($idRol)||strlen($idRol)==0){ 
				 throw new valorNuloInvalido('El campo idRol es requerido.');
			 }
 			$this->idRol=$idRol; 
		}
		function getIdUsuario(){ return $this->idUsuario; }
		function getIdRol(){ return $this->idRol; }
	}
?>