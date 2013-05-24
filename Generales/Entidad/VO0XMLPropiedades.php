<?php
	class VO0XMLPropiedades{
		protected $idXMLPropiedades=null;
		protected $nombre0XMLPropiedades=null;
		protected $tabla=null;
		protected $campo=null;
		protected $xmlPropiedades=null;
		function VO0XMLPropiedades($idXMLPropiedades=null,$nombre0XMLPropiedades=null,$tabla=null,$campo=null,$xmlPropiedades=null){
			if(!is_null($idXMLPropiedades)) $this->setIdXMLPropiedades($idXMLPropiedades);
			if(!is_null($nombre0XMLPropiedades)) $this->setNombre0XMLPropiedades($nombre0XMLPropiedades);
			if(!is_null($tabla)) $this->setTabla($tabla);
			if(!is_null($campo)) $this->setCampo($campo);
			if(!is_null($xmlPropiedades)) $this->setXmlPropiedades($xmlPropiedades);
		}
		function toString(){
			return 
			'idXMLPropiedades='.$this->idXMLPropiedades.', '.
			'nombre0XMLPropiedades='.$this->nombre0XMLPropiedades.', '.
			'tabla='.$this->tabla.', '.
			'campo='.$this->campo.', '.
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
			return json_encode(array('idXMLPropiedades'=>$this->idXMLPropiedades,'nombre0XMLPropiedades'=>$this->nombre0XMLPropiedades,'tabla'=>$this->tabla,'campo'=>$this->campo,'xmlPropiedades'=>$this->xmlPropiedades));
		}
		function setIdXMLPropiedades($idXMLPropiedades){ 
 			if(is_null($idXMLPropiedades)||strlen($idXMLPropiedades)==0){ 
				 throw new valorNuloInvalido('El campo idXMLPropiedades es requerido.');
			 }
 			$this->idXMLPropiedades=$idXMLPropiedades; 
		}
		function setNombre0XMLPropiedades($nombre0XMLPropiedades){ 
 			if(is_null($nombre0XMLPropiedades)||strlen($nombre0XMLPropiedades)==0){ 
				 throw new valorNuloInvalido('El campo nombre0XMLPropiedades es requerido.');
			 }
 			$this->nombre0XMLPropiedades=$nombre0XMLPropiedades; 
		}
		function setTabla($tabla){ 
 			if(is_null($tabla)||strlen($tabla)==0){ 
				 throw new valorNuloInvalido('El campo tabla es requerido.');
			 }
 			$this->tabla=$tabla; 
		}
		function setCampo($campo){ 
 			if(is_null($campo)||strlen($campo)==0){ 
				 throw new valorNuloInvalido('El campo campo es requerido.');
			 }
 			$this->campo=$campo; 
		}
		function setXmlPropiedades($xmlPropiedades){ 
 			if(is_null($xmlPropiedades)||strlen($xmlPropiedades)==0){ 
				 throw new valorNuloInvalido('El campo xmlPropiedades es requerido.');
			 }
 			$this->xmlPropiedades=$xmlPropiedades; 
		}
		function getIdXMLPropiedades(){ return $this->idXMLPropiedades; }
		function getNombre0XMLPropiedades(){ return $this->nombre0XMLPropiedades; }
		function getTabla(){ return $this->tabla; }
		function getCampo(){ return $this->campo; }
		function getXmlPropiedades(){ return $this->xmlPropiedades; }
	}
?>