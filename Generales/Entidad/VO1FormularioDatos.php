<?php
	class VO1FormularioDatos{
		protected $idFormularioDatos=null;
		protected $idFormulario=null;
		protected $xmlDatosEnvio=null;
		protected $xmlDatosFormulario=null;
		protected $activo=null;
		function VO1FormularioDatos($idFormularioDatos=null,$idFormulario=null,$xmlDatosEnvio=null,$xmlDatosFormulario=null,$activo=null){
			if(!is_null($idFormularioDatos)) $this->setIdFormularioDatos($idFormularioDatos);
			if(!is_null($idFormulario)) $this->setIdFormulario($idFormulario);
			if(!is_null($xmlDatosEnvio)) $this->setXmlDatosEnvio($xmlDatosEnvio);
			if(!is_null($xmlDatosFormulario)) $this->setXmlDatosFormulario($xmlDatosFormulario);
			if(!is_null($activo)) $this->setActivo($activo);
		}
		function toString(){
			return 
			'idFormularioDatos='.$this->idFormularioDatos.', '.
			'idFormulario='.$this->idFormulario.', '.
			'xmlDatosEnvio='.$this->xmlDatosEnvio.', '.
			'xmlDatosFormulario='.$this->xmlDatosFormulario.', '.
			'activo='.$this->activo.', ';
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
			return json_encode(array('idFormularioDatos'=>$this->idFormularioDatos,'idFormulario'=>$this->idFormulario,'xmlDatosEnvio'=>$this->xmlDatosEnvio,'xmlDatosFormulario'=>$this->xmlDatosFormulario,'activo'=>$this->activo));
		}
		function setIdFormularioDatos($idFormularioDatos){ 
 			if(is_null($idFormularioDatos)||strlen($idFormularioDatos)==0){ 
				 throw new valorNuloInvalido('El campo idFormularioDatos es requerido.');
			 }
 			$this->idFormularioDatos=$idFormularioDatos; 
		}
		function setIdFormulario($idFormulario){ 
 			if(is_null($idFormulario)||strlen($idFormulario)==0){ 
				 throw new valorNuloInvalido('El campo idFormulario es requerido.');
			 }
 			$this->idFormulario=$idFormulario; 
		}
		function setXmlDatosEnvio($xmlDatosEnvio){ 
 			if(is_null($xmlDatosEnvio)||strlen($xmlDatosEnvio)==0){ 
				 throw new valorNuloInvalido('El campo xmlDatosEnvio es requerido.');
			 }
 			$this->xmlDatosEnvio=$xmlDatosEnvio; 
		}
		function setXmlDatosFormulario($xmlDatosFormulario){ 
 			if(is_null($xmlDatosFormulario)||strlen($xmlDatosFormulario)==0){ 
				 throw new valorNuloInvalido('El campo xmlDatosFormulario es requerido.');
			 }
 			$this->xmlDatosFormulario=$xmlDatosFormulario; 
		}
		function setActivo($activo){ 
 			if(is_null($activo)||strlen($activo)==0){ 
				 throw new valorNuloInvalido('El campo activo es requerido.');
			 }
 			$this->activo=$activo; 
		}
		function getIdFormularioDatos(){ return $this->idFormularioDatos; }
		function getIdFormulario(){ return $this->idFormulario; }
		function getXmlDatosEnvio(){ return $this->xmlDatosEnvio; }
		function getXmlDatosFormulario(){ return $this->xmlDatosFormulario; }
		function getActivo(){ return $this->activo; }
	}
?>