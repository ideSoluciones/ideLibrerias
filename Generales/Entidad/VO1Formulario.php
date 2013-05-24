<?php
	class VO1Formulario{
		protected $idFormulario=null;
		protected $nombreFormulario=null;
		protected $xmlPropiedadesFormulario=null;
		protected $xmlCamposFormulario=null;
		function VO1Formulario($idFormulario=null,$nombreFormulario=null,$xmlPropiedadesFormulario=null,$xmlCamposFormulario=null){
			if(!is_null($idFormulario)) $this->setIdFormulario($idFormulario);
			if(!is_null($nombreFormulario)) $this->setNombreFormulario($nombreFormulario);
			if(!is_null($xmlPropiedadesFormulario)) $this->setXmlPropiedadesFormulario($xmlPropiedadesFormulario);
			if(!is_null($xmlCamposFormulario)) $this->setXmlCamposFormulario($xmlCamposFormulario);
		}
		function toString(){
			return 
			'idFormulario='.$this->idFormulario.', '.
			'nombreFormulario='.$this->nombreFormulario.', '.
			'xmlPropiedadesFormulario='.$this->xmlPropiedadesFormulario.', '.
			'xmlCamposFormulario='.$this->xmlCamposFormulario.', ';
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
			return json_encode(array('idFormulario'=>$this->idFormulario,'nombreFormulario'=>$this->nombreFormulario,'xmlPropiedadesFormulario'=>$this->xmlPropiedadesFormulario,'xmlCamposFormulario'=>$this->xmlCamposFormulario));
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
		function setXmlPropiedadesFormulario($xmlPropiedadesFormulario){ 
 			if(is_null($xmlPropiedadesFormulario)||strlen($xmlPropiedadesFormulario)==0){ 
				 throw new valorNuloInvalido('El campo xmlPropiedadesFormulario es requerido.');
			 }
 			$this->xmlPropiedadesFormulario=$xmlPropiedadesFormulario; 
		}
		function setXmlCamposFormulario($xmlCamposFormulario){ 
 			if(is_null($xmlCamposFormulario)||strlen($xmlCamposFormulario)==0){ 
				 throw new valorNuloInvalido('El campo xmlCamposFormulario es requerido.');
			 }
 			$this->xmlCamposFormulario=$xmlCamposFormulario; 
		}
		function getIdFormulario(){ return $this->idFormulario; }
		function getNombreFormulario(){ return $this->nombreFormulario; }
		function getXmlPropiedadesFormulario(){ return $this->xmlPropiedadesFormulario; }
		function getXmlCamposFormulario(){ return $this->xmlCamposFormulario; }
	}
?>