<?php
	class VO0Configuracion{
		protected $idConfiguracion=null;
		protected $idUsuario=null;
		protected $nombreConfiguracion=null;
		protected $xmlValor=null;
		function VO0Configuracion($idConfiguracion=null,$idUsuario=null,$nombreConfiguracion=null,$xmlValor=null){
			if(!is_null($idConfiguracion)) $this->setIdConfiguracion($idConfiguracion);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($nombreConfiguracion)) $this->setNombreConfiguracion($nombreConfiguracion);
			if(!is_null($xmlValor)) $this->setXmlValor($xmlValor);
		}
		function toString(){
			return 
			'idConfiguracion='.$this->idConfiguracion.', '.
			'idUsuario='.$this->idUsuario.', '.
			'nombreConfiguracion='.$this->nombreConfiguracion.', '.
			'xmlValor='.$this->xmlValor.', ';
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
			return json_encode(array('idConfiguracion'=>$this->idConfiguracion,'idUsuario'=>$this->idUsuario,'nombreConfiguracion'=>$this->nombreConfiguracion,'xmlValor'=>$this->xmlValor));
		}
		function setIdConfiguracion($idConfiguracion){ 
 			if(is_null($idConfiguracion)||strlen($idConfiguracion)==0){ 
				 throw new valorNuloInvalido('El campo idConfiguracion es requerido.');
			 }
 			$this->idConfiguracion=$idConfiguracion; 
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setNombreConfiguracion($nombreConfiguracion){ 
 			if(is_null($nombreConfiguracion)||strlen($nombreConfiguracion)==0){ 
				 throw new valorNuloInvalido('El campo nombreConfiguracion es requerido.');
			 }
 			$this->nombreConfiguracion=$nombreConfiguracion; 
		}
		function setXmlValor($xmlValor){ 
 			if(is_null($xmlValor)||strlen($xmlValor)==0){ 
				 throw new valorNuloInvalido('El campo xmlValor es requerido.');
			 }
 			$this->xmlValor=$xmlValor; 
		}
		function getIdConfiguracion(){ return $this->idConfiguracion; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getNombreConfiguracion(){ return $this->nombreConfiguracion; }
		function getXmlValor(){ return $this->xmlValor; }
	}
?>