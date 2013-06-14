<?php
	class VO1FormularioDatos{
		protected $idFormularioDatos=null;
		protected $idFormulario=null;
		protected $datosEnvio=null;
		protected $datosFormulario=null;
		function VO1FormularioDatos($idFormularioDatos=null,$idFormulario=null,$datosEnvio=null,$datosFormulario=null){
			if(!is_null($idFormularioDatos)) $this->setIdFormularioDatos($idFormularioDatos);
			if(!is_null($idFormulario)) $this->setIdFormulario($idFormulario);
			if(!is_null($datosEnvio)) $this->setDatosEnvio($datosEnvio);
			if(!is_null($datosFormulario)) $this->setDatosFormulario($datosFormulario);
		}
		function toString(){
			return 
			'idFormularioDatos='.$this->idFormularioDatos.', '.
			'idFormulario='.$this->idFormulario.', '.
			'datosEnvio='.$this->datosEnvio.', '.
			'datosFormulario='.$this->datosFormulario.', ';
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
			return json_encode(array('idFormularioDatos'=>$this->idFormularioDatos,'idFormulario'=>$this->idFormulario,'datosEnvio'=>$this->datosEnvio,'datosFormulario'=>$this->datosFormulario));
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
		function setDatosEnvio($datosEnvio){ 
 			if(is_null($datosEnvio)||strlen($datosEnvio)==0){ 
				 throw new valorNuloInvalido('El campo datosEnvio es requerido.');
			 }
 			$this->datosEnvio=$datosEnvio; 
		}
		function setDatosFormulario($datosFormulario){ 
 			if(is_null($datosFormulario)||strlen($datosFormulario)==0){ 
				 throw new valorNuloInvalido('El campo datosFormulario es requerido.');
			 }
 			$this->datosFormulario=$datosFormulario; 
		}
		function getIdFormularioDatos(){ return $this->idFormularioDatos; }
		function getIdFormulario(){ return $this->idFormulario; }
		function getDatosEnvio(){ return $this->datosEnvio; }
		function getDatosFormulario(){ return $this->datosFormulario; }
	}
?>