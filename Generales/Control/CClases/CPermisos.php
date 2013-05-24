<?php

	class CPermisos{
	
		private $CPropiedades;
		
		function CPermisos($propiedades=array()){
			$this->CPropiedades=new CPropiedades();
			if(is_array($propiedades)){
				foreach($propiedades as $nombre=>$valor){
					if(is_string($nombre)&&is_bool($valor)){
						$this->CPropiedades->addPropiedad($nombre,$valor);
					}
				}
			}
		}
		
		function setPermiso($nombre,$valor){
			if(is_string($nombre)&&is_bool($valor)){
				$this->CPropiedades->setPropiedad($nombre,$valor);
			}
		}
		
		function getPermiso($nombre){
			$propiedad=$this->CPropiedades->getPropiedad($nombre);
			if(is_bool($propiedad)){
				return $propiedad;
			}else{
				return false;
			}
		}
	}
?>
