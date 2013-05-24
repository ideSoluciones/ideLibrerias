<?php
	class VO0Sesion{
		protected $idSesion=null;
		protected $datosSesion=null;
		protected $idUsuario=null;
		protected $ultimoAcceso=null;
		protected $datosAcceso=null;
		function VO0Sesion($idSesion=null,$datosSesion=null,$idUsuario=null,$ultimoAcceso=null,$datosAcceso=null){
			if(!is_null($idSesion)) $this->setIdSesion($idSesion);
			if(!is_null($datosSesion)) $this->setDatosSesion($datosSesion);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($ultimoAcceso)) $this->setUltimoAcceso($ultimoAcceso);
			if(!is_null($datosAcceso)) $this->setDatosAcceso($datosAcceso);
		}
		function toString(){
			return 
			'idSesion='.$this->idSesion.', '.
			'datosSesion='.$this->datosSesion.', '.
			'idUsuario='.$this->idUsuario.', '.
			'ultimoAcceso='.$this->ultimoAcceso.', '.
			'datosAcceso='.$this->datosAcceso.', ';
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
			return json_encode(array('idSesion'=>$this->idSesion,'datosSesion'=>$this->datosSesion,'idUsuario'=>$this->idUsuario,'ultimoAcceso'=>$this->ultimoAcceso,'datosAcceso'=>$this->datosAcceso));
		}
		function setIdSesion($idSesion){ 
 			if(is_null($idSesion)||strlen($idSesion)==0){ 
				 throw new valorNuloInvalido('El campo idSesion es requerido.');
			 }
 			$this->idSesion=$idSesion; 
		}
		function setDatosSesion($datosSesion){ 
 			if(is_null($datosSesion)||strlen($datosSesion)==0){ 
				 throw new valorNuloInvalido('El campo datosSesion es requerido.');
			 }
 			$this->datosSesion=$datosSesion; 
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setUltimoAcceso($ultimoAcceso){ 
 			if(is_null($ultimoAcceso)||strlen($ultimoAcceso)==0){ 
				 throw new valorNuloInvalido('El campo ultimoAcceso es requerido.');
			 }
 			$this->ultimoAcceso=$ultimoAcceso; 
		}
		function setDatosAcceso($datosAcceso=null){ 
  			$this->datosAcceso=$datosAcceso; 
		}
		function getIdSesion(){ return $this->idSesion; }
		function getDatosSesion(){ return $this->datosSesion; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getUltimoAcceso(){ return $this->ultimoAcceso; }
		function getDatosAcceso(){ return $this->datosAcceso; }
	}
?>