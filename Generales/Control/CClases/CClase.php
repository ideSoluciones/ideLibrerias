<?php

	class CClase{
	
		private $campos=array();
		private $CCampos=array();
		private $propiedades;
		private $llavesPrimarias=array();
		private $llavesForaneas=array();
		private $referencias=array();
		private $xmlClase=null;
		private $desactivable=false;
		
		function CClase($xmlClase=null){
			$this->campos=array();
			$this->propiedades=new CPropiedades();
			$this->setXMLClase($xmlClase);
		}
		
		function setXMLClase($xmlClase){
			if(!is_null($xmlClase)){
				$this->xmlClase=null;
				if(is_string($xmlClase)){
					try{
						$this->xmlClase=@new SimpleXMLElement($xmlClase);
					}catch(Exception $e){
						$this->xmlClase=null;
					}
				}else if(is_object($xmlClase)){
					if(strcmp(get_class($xmlClase),"SimpleXMLElement")==0){
						$this->xmlClase=$xmlClase;
					}
				}
				if(!is_null($this->xmlClase)){
					$this->desactivable=false;
					$this->procesarXmlClase($this->xmlClase);
				}
			}
		}
		
		function esDesactivable(){
			return $this->desactivable;
		}
		
		function getLlaves(){
			if(count($this->llavesPrimarias)<=0){
				return $this->campos;
			}else{
				$respuesta=array();	
				foreach($this->llavesPrimarias as $nombre=>$valor){
					$respuesta["$nombre"]=$this->campos["$nombre"];
				}
				return $respuesta;
			}
		}
		
		function getLlavesPrimarias(){
			$respuesta=array();
			foreach($this->llavesPrimarias as $nombre=>$valor){
				$respuesta["$nombre"]=$this->campos["$nombre"];
			}
			return $respuesta;
		}
		
		function getLlavesForaneas(){
			$respuesta=array();
			foreach($this->llavesForaneas as $nombre=>$valor){
				$respuesta["$nombre"]=$this->campos["$nombre"];
			}
			return $respuesta;
		}
		
		function getCampos(){
			return $this->campos;
		}

		function getCCampos(){
			return $this->CCampos;
		}
		
		function getNoCampos(){
			return count($this->campos);
		}
		
		function getCamposNoLlave(){
			$respuesta=array();	
			foreach($this->campos as $idCampo=>$campo){
				if(!array_key_exists($idCampo,$this->llavesPrimarias) && !array_key_exists($idCampo,$this->llavesForaneas)){
					$respuesta["$idCampo"]=$campo;
				}
			}
			return $respuesta;
		}
		
		function getPropiedades(){
			return $this->propiedades;
		}
		
		function getPropiedad($nombre){
			return $this->propiedades->getPropiedad($nombre);
		}
		
		function setPropiedad($nombre,$valor){
			$this->propiedades->setPropiedad($nombre,$valor);
		}
		
		function getReferencias(){
			return $this->referencias;
		}
		
		function procesarXmlClase($xml){
			static $ultimoCampo;
			foreach($xml->children() as $hijo){
				switch($hijo->getName()){
					case "Propiedades":
						$propiedades=$hijo->attributes();
						foreach($propiedades as $nombre=>$valor){
							$this->propiedades->addPropiedad($nombre,$valor);
						}
						$this->procesarXmlClase($hijo);
						break;
					case "Propiedad":
						$campo=new CPropiedades();
						$ccampo=new CCampo();
						$llavePrimaria=false;
						$llaveForanea=false;
						$nombre="";
						//Se genera el idCampo, para busqueda posterior de llaves
						$idCampo="C".count($this->campos);
						$campo->addPropiedad("idCampo",$idCampo);
						//Se recorren todos los atributos del campo y se identifica si es llave
						foreach($hijo->attributes() as $nombre => $valor) {
							$ccampo->set($nombre,(string)$valor);
							switch($nombre){
								case "llavePrimaria":
									$llavePrimaria=true;
									break;
								case "llaveForanea":
									$llaveForanea=true;
									break;
								case "nombre":
									//$nombre=$valor;
									if(strcmp("activo",$valor)==0){
										$this->desactivable=true;
									}
									break;
							}
							//Se agrega la propiedad al objeto CPropiedades
							$campo->addPropiedad($nombre,(string)$valor);
						}
						if($llavePrimaria){
							$this->llavesPrimarias["$idCampo"]="llavePrimaria";
						}
						if($llaveForanea){
							$this->llavesForaneas["$idCampo"]="llaveForanea";
						}
						//Se buscan todos los valores(Dominio) del campo
						$valores=$hijo->xpath('Valores/Valor');
						//Se crea un arreglo con los valores
						$valoresArr=array();
						foreach($valores as $valor){
							$valoresArr[]=array($valor["nombre"],$valor["valor"]);
						}
						//Si el arreglo de valores contiene elementos se agrega al objeto CPropiedades
						if(count($valoresArr)>0){
							$campo->addPropiedad("valores",$valoresArr);
							$ccampo->set("valores",$valoresArr);
						}
						$this->campos["$idCampo"]=$campo;

						$this->CCampos["$idCampo"]=$ccampo;

						break;
					case "Referencias":
						$this->procesarXmlClase($hijo);
						break;
					case "Referencia":
						$this->referencias[]=$hijo["tabla"];
						break;
				}
			}
		}
		
		function toString(){
			$text="_recuadro_";
				$text.="_leyenda_Campos_finLeyenda_";
				foreach($this->campos as $campo){
					$text.="_caja_";
						$text.=$campo->toString();
					$text.="_finCaja_";
				}
				$text.="_caja__negrilla_Llaves_finNegrilla__finCaja_";
				foreach($this->llavesPrimarias as $nombre=>$tipo){
					$propiedades=$this->campos[$nombre]->getPropiedades();
					$nombrePropiedad=$propiedades[0];
					if(in_array("nombre",$propiedades)){
						$nombrePropiedad="nombre";
					}
					if(isset($this->campos[$nombre])){
						$text.="_caja_{$this->campos[$nombre]->getPropiedad($nombrePropiedad)} -> $tipo _finCaja_";
					}
				}
				foreach($this->llavesForaneas as $nombre=>$tipo){
					$propiedades=$this->campos[$nombre]->getPropiedades();
					$nombrePropiedad=$propiedades[0];
					if(in_array("nombre",$propiedades)){
						$nombrePropiedad="nombre";
					}
					var_dump($this->campos);
					if(isset($this->campos[$nombre])){
						$text.="_caja_{$this->campos[$nombre]->getPropiedad($nombrePropiedad)} -> $tipo _finCaja_";
					}
				}
				$text.="_caja__negrilla_Referencias_finNegrilla__finCaja_";
				foreach($this->referencias as $nombre){
					$text.="_caja_{$nombre}_finCaja_";
				}
				$text.="_caja__negrilla_Propiedades de clase_finNegrilla__finCaja_";
				$text.=$this->propiedades->toString();
				
			$text.="_finRecuadro_";
			return $text;
		}
	}
?>
