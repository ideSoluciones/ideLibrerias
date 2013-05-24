<?php
	class VO1Puntaje{
		protected $idPuntaje=null;
		protected $idUsuario=null;
		protected $tiempo=null;
		protected $puntaje=null;
		protected $nivel=null;
		protected $nombreJuego=null;
		protected $xmlPropiedades=null;
		function VO1Puntaje($idPuntaje=null,$idUsuario=null,$tiempo=null,$puntaje=null,$nivel=null,$nombreJuego=null,$xmlPropiedades=null){
			if(!is_null($idPuntaje)) $this->setIdPuntaje($idPuntaje);
			if(!is_null($idUsuario)) $this->setIdUsuario($idUsuario);
			if(!is_null($tiempo)) $this->setTiempo($tiempo);
			if(!is_null($puntaje)) $this->setPuntaje($puntaje);
			if(!is_null($nivel)) $this->setNivel($nivel);
			if(!is_null($nombreJuego)) $this->setNombreJuego($nombreJuego);
			if(!is_null($xmlPropiedades)) $this->setXmlPropiedades($xmlPropiedades);
		}
		function toString(){
			return 
			'idPuntaje='.$this->idPuntaje.', '.
			'idUsuario='.$this->idUsuario.', '.
			'tiempo='.$this->tiempo.', '.
			'puntaje='.$this->puntaje.', '.
			'nivel='.$this->nivel.', '.
			'nombreJuego='.$this->nombreJuego.', '.
			'xmlPropiedades='.$this->xmlPropiedades.', ';
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
			return json_encode(array('idPuntaje'=>$this->idPuntaje,'idUsuario'=>$this->idUsuario,'tiempo'=>$this->tiempo,'puntaje'=>$this->puntaje,'nivel'=>$this->nivel,'nombreJuego'=>$this->nombreJuego,'xmlPropiedades'=>$this->xmlPropiedades));
		}
		function setIdPuntaje($idPuntaje){ 
 			if(is_null($idPuntaje)||strlen($idPuntaje)==0){ 
				 throw new valorNuloInvalido('El campo idPuntaje es requerido.');
			 }
 			$this->idPuntaje=$idPuntaje; 
		}
		function setIdUsuario($idUsuario){ 
 			if(is_null($idUsuario)||strlen($idUsuario)==0){ 
				 throw new valorNuloInvalido('El campo idUsuario es requerido.');
			 }
 			$this->idUsuario=$idUsuario; 
		}
		function setTiempo($tiempo){ 
 			if(is_null($tiempo)||strlen($tiempo)==0){ 
				 throw new valorNuloInvalido('El campo tiempo es requerido.');
			 }
 			$this->tiempo=$tiempo; 
		}
		function setPuntaje($puntaje){ 
 			if(is_null($puntaje)||strlen($puntaje)==0){ 
				 throw new valorNuloInvalido('El campo puntaje es requerido.');
			 }
 			$this->puntaje=$puntaje; 
		}
		function setNivel($nivel){ 
 			if(is_null($nivel)||strlen($nivel)==0){ 
				 throw new valorNuloInvalido('El campo nivel es requerido.');
			 }
 			$this->nivel=$nivel; 
		}
		function setNombreJuego($nombreJuego){ 
 			if(is_null($nombreJuego)||strlen($nombreJuego)==0){ 
				 throw new valorNuloInvalido('El campo nombreJuego es requerido.');
			 }
 			$this->nombreJuego=$nombreJuego; 
		}
		function setXmlPropiedades($xmlPropiedades=null){ 
  			$this->xmlPropiedades=$xmlPropiedades; 
		}
		function getIdPuntaje(){ return $this->idPuntaje; }
		function getIdUsuario(){ return $this->idUsuario; }
		function getTiempo(){ return $this->tiempo; }
		function getPuntaje(){ return $this->puntaje; }
		function getNivel(){ return $this->nivel; }
		function getNombreJuego(){ return $this->nombreJuego; }
		function getXmlPropiedades(){ return $this->xmlPropiedades; }
	}
?>