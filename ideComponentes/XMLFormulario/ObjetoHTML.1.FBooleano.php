<?php

	/**
	*    @name FBooleano
	*    @abstract	
	*    @author Felipe Cano <fcano@idesoluciones.com >
	*    @version 1.0
	*/

	class FBooleano extends ObjetoHTML{

		function FBooleano($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}

		function toHTML(){
		
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			$otrosAtributos="";
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"funcionCambio")==0){
					$otrosAtributos.=" onChange='".$i."(this);'";
				}elseif (strcmp($a,"estilo")==0){
					$otrosAtributos.=" style='".$i."' ";
				}else{
					//$otrosAtributos.=" ".$a."=".$i;
				}
			}
			$atributos="";
			if (isset($propiedades["soloLectura"])){
				$otrosAtributos.=" readonly=true disabled ";
			}

			$total="";
			if (isset($propiedades["titulo"]))
			$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
			$total.="		<input id='".$this->getIdObjeto()."' class='".$estilo."Booleano' $otrosAtributos type='checkbox' name='".$this->getNombre()."'
							value='true'
							".(
								$propiedades["valorPorDefecto"]=='1' || 
								$propiedades["valorPorDefecto"]==true || 
								$propiedades["valorPorDefecto"]=='true'
								?"checked=true":""
							)." $atributos />";

			return $total;
		}
	}
	
?>
