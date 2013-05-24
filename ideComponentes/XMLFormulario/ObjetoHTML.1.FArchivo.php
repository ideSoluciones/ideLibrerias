<?php

	/**
	*    @name FArchivo
	*    @abstract	
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com>
	*    @version 1.0
	*/

	class FArchivo extends ObjetoHTML{

		function FArchivo($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}

		function toHTML(){
			$total="";
			$atributos="";
			
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";

			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos=$a."=".$i;
				}
			}
			$id="";
			if(isset($propiedades["id"])){
				$id=$propiedades["id"];
			}
			$default="";
			if(isset($propiedades["valorPorDefecto"])){
				$default=$propiedades["valorPorDefecto"];
			}
			$nombre=$this->getNombre();
			if(isset($propiedades["nombre"])){
				$nombre=$propiedades["nombre"];
			}
			if(isset($propiedades["titulo"])){
				if (strlen($propiedades["titulo"])>0){
					$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
				}
			}
			$total.="	<input id='".$id."' class='".$estilo."Campo' type='file' name='".$nombre."' value='".$default."' ".$atributos." />\n";
			return $total;
		}
	}

?>
