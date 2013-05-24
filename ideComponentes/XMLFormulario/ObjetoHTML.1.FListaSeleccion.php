<?php

	/**
	*    @name FListaSeleccion
	*    @abstract	
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FListaSeleccion extends ObjetoHTML{
	
		function FListaSeleccion($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
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
			$style="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$estilo.="contenedorCampoFormulario".$propiedades["tipo"]." campoFormulario";
						
			$atributos="";
			$propiedadesSelect="";
			$otrosAtributos="";
			foreach ($propiedades as $a => $i){
				if (strcmp($a,"autocomplete")==0 && strcmp($i,"off")==0){
					$atributos.=$a."=".$i." ";
				}else{
					if(strcmp($a,"onChange")==0)
						$propiedadesSelect.=$a."='".$i."' ";
					else{
						switch($a){
							case "opciones":case "tipo":case "campo":case "id":case "nombre":case "titulo":case "requerido":case "valorPorDefecto":case "error":break;
							default: $otrosAtributos.=" ".$a."='".$i."'";
						}
					}
				}
			}
			$total="";
			$multiseleccion=false;
			if (isset($propiedades["multiseleccion"])){
				if ($propiedades["multiseleccion"]=="true"){
					$this->agregarCss("Externos/jquery/jquery.dropdown-check-list/css/ui.dropdownchecklist.css");
					$this->agregarJs("Externos/jquery/jquery.dropdown-check-list/js/ui.dropdownchecklist-min.js");
					$total="
						<script type='text/javascript'>
							$(function() {
									$('#".$this->getIdObjeto()."').dropdownchecklist();
							});
						</script>
					";
					$multiseleccion=true;
				}
			}
			$total.="";
			if (isset($propiedades["titulo"])){
				if (strlen((string)$propiedades["titulo"])>0)
					$total.="	<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario ".$estilo."TituloListaSeleccion'>".$propiedades["titulo"];
			}
			if (isset($propiedades["titulo"])){
				if (strlen((string)$propiedades["titulo"])>0)
					$total.="</div>\n";
			}
			if (isset($propiedades["ayuda"])){
				$ayuda=xml::add(null, "Ayuda");
				xml::add($ayuda, "Wiki", $propiedades["ayuda"]);
				$total.=ide::renderizar($ayuda);
			}
			$total.="		<select class='".$estilo."ListaSeleccion' $otrosAtributos $propiedadesSelect id='".$propiedades["id"]."' ".($multiseleccion?"multiple":"")." name='".$this->getNombre()."".($multiseleccion?"[]":"")."' $style >";
			if(isset($propiedades["opciones"])){
				if(is_array($propiedades["opciones"])){
					foreach($propiedades["opciones"] as $nom=>$valor){
						$selected="";
						if ($multiseleccion){
							$valoresPorDefecto=explode(",", $propiedades["valorPorDefecto"]);
							if (in_array($valor, $valoresPorDefecto)){
								$selected="selected";
							}
						}else{
							if(strcmp($propiedades["valorPorDefecto"],$valor)==0) $selected="selected";
						}
						$total.="<option value='".$valor."' $selected >".$nom."</option>";
					}
				}
			}
			$total.="</select>";
			return $total;
		}
	}
	
?>
