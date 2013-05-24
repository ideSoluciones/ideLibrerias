<?php

	/**
	*    @name FBusquedaSeleccion
	*    @abstract	
	*    @author Jorge Gonzalez <jgonzalez@idesoluciones.com >
	*    @version 1.0
	*/
	
	class FBusquedaSeleccion extends ObjetoHTML{

		function FBusquedaSeleccion($idObjeto, $nombre, $propiedades, $tipoSalida, $accionPlus){
			$this->setIdObjeto($idObjeto);
			$this->setNombre($nombre);
			$this->setPropiedades($propiedades);
			$this->setTipoSalida($tipoSalida);
			$this->setAccionPlus($accionPlus);
		}

		function toHTML(){
			$total="";
			$atributos="";
			$totalhtml="";
			$js="";
			
			$propiedades=$this->getPropiedades();
			
			$id="";
			if(isset($propiedades["id"])){
				$id=$propiedades["id"];
			}

			$estilo="";
			if (isset($propiedades["error"])){
				if (strcmp(strtolower($propiedades["error"]),"true")==0){
					$estilo.=($propiedades["error"]==true?"error ":"");
				}
			}
			$tipo="";
			if(isset($propiedades["tipo"])){
				$tipo=$propiedades["tipo"];
			}
			$estilo.="contenedorCampoFormulario".$tipo." campoFormulario";
			
			$otrosAtributos="";
			foreach ($propiedades as $a => $i){
				switch($a){
					case "tipo":case "id":case "error":case "campo":case "opciones":case "nombre":case "titulo":case "valorPorDefecto":break;
					default: $otrosAtributos.=" ".$a."=".$i;			
				}
			}
			
			$totalhtml="";
			if (isset($propiedades["titulo"])){
				$totalhtml.="<div id='".$this->getIdObjeto()."Titulo' class='".$estilo."Titulo  etiquetaFormulario'>".$propiedades["titulo"]."</div>\n";
			}
			$totalhtml.="<input type='hidden' id='".$id."' $otrosAtributos name='".$this->getNombre()."' />";
			$totalhtml.="<input type='text' autocomplete='off' class='".$estilo."BusquedaSeleccion' id='".$id."_Etiqueta'  name='".$this->getNombre()."_Etiqueta' />";

			$js.="<script>
				$(function(){";
			if(is_array($propiedades["opciones"])){
				$js.="\nvar ".$this->getIdObjeto()."_ValoresCampo = [";
				foreach($propiedades["opciones"] as $nom=>$valor){
					$js.="{label:'".$nom."', value:'".$valor."'}, \n";
				}
				$js.='];
					$("#'.$id.'_Etiqueta").autocomplete({
						source:'.$this->getIdObjeto().'_ValoresCampo,
						select: function(event, ui) {
									alert("Actualizando valor");
									$("#'.$id.'").val(ui.item.value);
									$("#'.$id.'_Etiqueta").val(ui.item.label);
									return false;
								},
						focus: function(event, ui) {
							$("#'.$id.'_Etiqueta").val(ui.item.label);
							return false;
						},

					});';
			}
				
			$js.='});
			</script>';
			$totalhtml.="";
			$total=$js.$totalhtml;
			return $total;
		}
	}
?>
