<?php
	class VO1Formulario{
		protected $idFormulario=null;
		protected $nombreFormulario=null;
		protected $propiedadesFormulario=null;
		protected $camposFormulario=null;
		function VO1Formulario($idFormulario=null,$nombreFormulario=null,$propiedadesFormulario=null,$camposFormulario=null){
			if(!is_null($idFormulario)) $this->setIdFormulario($idFormulario);
			if(!is_null($nombreFormulario)) $this->setNombreFormulario($nombreFormulario);
			if(!is_null($propiedadesFormulario)) $this->setPropiedadesFormulario($propiedadesFormulario);
			if(!is_null($camposFormulario)) $this->setCamposFormulario($camposFormulario);
		}
		function toString(){
			return 
			'idFormulario='.$this->idFormulario.', '.
			'nombreFormulario='.$this->nombreFormulario.', '.
			'propiedadesFormulario='.$this->propiedadesFormulario.', '.
			'camposFormulario='.$this->camposFormulario.', ';
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
			return json_encode(array('idFormulario'=>$this->idFormulario,'nombreFormulario'=>$this->nombreFormulario,'propiedadesFormulario'=>$this->propiedadesFormulario,'camposFormulario'=>$this->camposFormulario));
		}
		function setIdFormulario($idFormulario){ 
 			if(is_null($idFormulario)||strlen($idFormulario)==0){ 
				 throw new valorNuloInvalido('El campo idFormulario es requerido.');
			 }
 			$this->idFormulario=$idFormulario; 
		}
		function setNombreFormulario($nombreFormulario){ 
 			if(is_null($nombreFormulario)||strlen($nombreFormulario)==0){ 
				 throw new valorNuloInvalido('El campo nombreFormulario es requerido.');
			 }
 			$this->nombreFormulario=$nombreFormulario; 
		}
		function setPropiedadesFormulario($propiedadesFormulario){ 
 			if(is_null($propiedadesFormulario)||strlen($propiedadesFormulario)==0){ 
				 throw new valorNuloInvalido('El campo propiedadesFormulario es requerido.');
			 }
 			$this->propiedadesFormulario=$propiedadesFormulario; 
		}
		function setCamposFormulario($camposFormulario){ 
 			if(is_null($camposFormulario)||strlen($camposFormulario)==0){ 
				 throw new valorNuloInvalido('El campo camposFormulario es requerido.');
			 }
 			$this->camposFormulario=$camposFormulario; 
		}
		function getIdFormulario(){ return $this->idFormulario; }
		function getNombreFormulario(){ return $this->nombreFormulario; }
		function getPropiedadesFormulario(){ return $this->propiedadesFormulario; }
		function getCamposFormulario(){ return $this->camposFormulario; }
	}
?>