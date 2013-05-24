<?php

	/**
	*    @name FRadios
	*    @abstract	
	*    @author 
	*    @version 1.0
	*/
	
	class FRadios extends ObjetoHTML{

		//function FRadios($nombre=null,$idObjeto=null,$titulo=null,$valorPorDefecto=null,$claseEstilo=null,$filas=null,$columnas=null){
		function FRadios($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}

		function toHTML(){
			$total="";
			$propiedades=$this->getPropiedades();
			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$otrosAtributos="";
			foreach($propiedades as $a => $i){
				switch($a){
					case "opciones":case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error":break;
					default: $otrosAtributos.=" ".$a."='".$i."'";
				}
			}
			
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
			
				$total="<fieldset
						id='".$this->getIdObjeto()."'
						class='".$estilo."Radios' >".(isset($propiedades["titulo"])?"<legend class='etiquetaFormulario etiquetaFormularioRadio'>".$propiedades["titulo"]."</legend>":"");
				foreach($propiedades["opciones"] as $nom=>$valor){
					$total.="<div id='".$this->getIdObjeto()."Contenedor' class='".$estilo."Radio' >
						<input onpress='variable_".$this->getIdObjeto()."Campo=".$valor."' id='".$propiedades["id"]."'  type='radio' name='".$this->getNombre()."' value='".$valor."' >
						<span class='campoFormularioNombreRadio'>".$nom."</span>
					</div>";
				}
				$total.="</fieldset>";
	
			//echo $total; exit(0);
			return $total;
		}
	}
	
?>
