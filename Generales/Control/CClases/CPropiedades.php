<?php

	class CPropiedades{
	
		private $propiedades=array();
		
		function CPropiedades($propiedades=array()){
			if(is_array($propiedades)){
				$this->propiedades=$propiedades;
			}
		}
		
		function setPropiedad($nombre,$valor){
			if(array_key_exists($nombre,$this->propiedades)){
				$this->propiedades["$nombre"]=siNoVacio($valor);
			}
		}
		
		function getPropiedad($nombre){
			$propiedad="";
			if(array_key_exists($nombre,$this->propiedades)){
				$propiedad=$this->propiedades["$nombre"];
			}
			return $propiedad;
		}
		
		function addPropiedad($nombre,$valor){
			$this->propiedades["$nombre"]=$valor;
		}
		
		function getPropiedades(){
			return array_keys($this->propiedades);
		}
		
		function noPropiedades(){
			return count($this->ropiedades);
		}
		
		function cPropiedadToJSon(){
			return json_encode($this->propiedades);
		}
		
		function jSonToCPropiedad($json){
			if(strlen($json)>0){
				$this->propiedades=json_decode($json, true);
			}
		}
		
		function obtenerListadoGrafico($soloNombre=false){
			$text ="_recuadro_";
				$text.="_tabla_";
				if(is_array($this->propiedades)){
					foreach($this->propiedades as $nombre=>$valor){
						$text.="_fila_";
							$text.="_celda_$nombre _finCelda_";
							if(!$soloNombre){
								$text.="_celda_$valor _finCelda_";
							}
						$text.="_finFila_";
					}
				}
				$text.="_finTabla_";
			$text.="_finRecuadro_";
			return $text;
		}
		
		function toString(){
			$text ="_recuadro_";
				$text.="_leyenda_CPropiedadClase_finLeyenda_";
				foreach($this->propiedades as $nombre=>$valor){
					$text.="_caja_";
						$text.=revisarArreglo($valor,$nombre,"ideProyecto");
					$text.="_finCaja_";
				}
			$text.="_finRecuadro_";
			return $text;
		}

	}

	class CTablaDatos{
		private $indice=array();
		private $datos=array();
		private function getClave(){
			$generada=false;
			while(!$generada){
				$clave=mt_rand();
				if(!array_key_exists($clave,$this->indice)){
					$generada=true;
				}
			}
			return $clave;
		}
		function add($nombre,$valor){
			$clave=$this->getClave();
			$this->datos[$clave]=array($nombre=>$valor);
			return $clave;
		}
	}
?>
